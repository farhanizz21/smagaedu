<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class Perangkat_model extends CI_Model {

    private $jenis_file_options = array(
        'modul' => 'Modul',
        'atp' => 'ATP',
        'rme' => 'RME',
        'prota' => 'Prota',
        'promes' => 'Promes',
        'kktp' => 'KKTP',
        'tp' => 'TP',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('guru_model');
    }

    public function get_jenis_file_options()
    {
        return $this->jenis_file_options;
    }

    public function compress_pdf_if_needed($file_path)
    {
        $original_size = filesize($file_path);
        if ($original_size === FALSE) {
            return array('success' => FALSE);
        }

        if ($original_size <= 2 * 1024 * 1024) {
            return array('success' => TRUE, 'compressed' => FALSE);
        }

        if (!function_exists('proc_open')) {
            return array('success' => FALSE);
        }

        $ghostscript = getenv('GHOSTSCRIPT_BINARY');
        if (empty($ghostscript)) {
            foreach (array('/usr/local/bin/gs', '/opt/homebrew/bin/gs', '/usr/bin/gs') as $candidate) {
                if (is_executable($candidate)) {
                    $ghostscript = $candidate;
                    break;
                }
            }
        }
        $ghostscript = $ghostscript ?: 'gs';

        $output_path = tempnam(dirname($file_path), 'perangkat-pdf-');
        if ($output_path === FALSE) {
            return array('success' => FALSE);
        }
        unlink($output_path);

        $command = array(
            $ghostscript,
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            '-dPDFSETTINGS=/ebook',
            '-dSAFER',
            '-dNOPAUSE',
            '-dBATCH',
            '-dQUIET',
            '-sOutputFile=' . $output_path,
            $file_path,
        );
        $process = proc_open($command, array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        ), $pipes);

        if (!is_resource($process)) {
            return array('success' => FALSE);
        }

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $process_error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exit_code = proc_close($process);

        if ($exit_code !== 0 || !is_file($output_path) || filesize($output_path) === 0) {
            log_message('error', 'Kompresi PDF perangkat gagal: ' . trim($process_error));
            if (is_file($output_path)) {
                unlink($output_path);
            }
            return array('success' => FALSE);
        }

        if (filesize($output_path) >= $original_size) {
            unlink($output_path);
            return array('success' => TRUE, 'compressed' => FALSE);
        }

        if (!rename($output_path, $file_path)) {
            unlink($output_path);
            return array('success' => FALSE);
        }

        return array('success' => TRUE, 'compressed' => TRUE);
    }

    private function get_mapel_uuids_by_guru($guru_uuid)
    {
        return $this->guru_model->get_mapel_uuid_list($guru_uuid);
    }

    public function get_mapel_by_guru($guru_uuid, $limit = NULL, $offset = 0)
    {
        $uuid_array = $this->get_mapel_uuids_by_guru($guru_uuid);
        if (empty($uuid_array)) {
            return [];
        }
        
        $this->db->where_in('uuid', $uuid_array);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('nama', 'ASC');
        if ($limit !== NULL) {
            $this->db->limit((int) $limit, (int) $offset);
        }
        return $this->db->get('mapel')->result();
    }

    public function count_mapel_by_guru($guru_uuid)
    {
        $uuid_array = $this->get_mapel_uuids_by_guru($guru_uuid);
        if (empty($uuid_array)) {
            return 0;
        }

        $this->db->where_in('uuid', $uuid_array);
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->count_all_results('mapel');
    }

    public function get_all_mapel($limit = NULL, $offset = 0)
    {
        if (has_role('admin')) {
            $mapel_uuids = $this->_get_admin_accessible_mapel_uuids();
            if (empty($mapel_uuids)) {
                return [];
            }

            $this->db->where_in('uuid', $mapel_uuids);
        }
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('nama', 'ASC');
        if ($limit !== NULL) {
            $this->db->limit((int) $limit, (int) $offset);
        }
        return $this->db->get('mapel')->result();
    }

    public function count_all_mapel()
    {
        if (has_role('admin')) {
            $mapel_uuids = $this->_get_admin_accessible_mapel_uuids();
            if (empty($mapel_uuids)) {
                return 0;
            }
            $this->db->where_in('uuid', $mapel_uuids);
        }
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->count_all_results('mapel');
    }

    private function _get_admin_accessible_mapel_uuids()
    {
        $creator_uuids = admin_visible_creator_uuids();
        if (empty($creator_uuids)) {
            return [];
        }

        $mapel_uuids = [];
        $this->db->select('uuid');
        $this->db->where_in('created_by', $creator_uuids);
        $this->db->where('deleted_at', NULL, FALSE);
        foreach ($this->db->get('mapel')->result() as $mapel) {
            $mapel_uuids[] = $mapel->uuid;
        }

        $this->db->select('uuid');
        $this->db->where('EXISTS (SELECT 1 FROM user_roles WHERE user_roles.user_id = users.id AND user_roles.role_id = 3)', NULL, FALSE);
        $this->db->where('created_by', $this->session->userdata('uuid'));
        $this->db->where('deleted_at', NULL, FALSE);
        foreach ($this->db->get('users')->result() as $teacher) {
            foreach ($this->get_mapel_by_guru($teacher->uuid) as $mapel) {
                $mapel_uuids[] = $mapel->uuid;
            }
        }

        if (has_role('guru')) {
            foreach ($this->get_mapel_by_guru($this->session->userdata('uuid')) as $mapel) {
                $mapel_uuids[] = $mapel->uuid;
            }
        }

        $this->db->select('DISTINCT mapel_uuid', FALSE);
        $this->db->where_in('guru_uuid', $creator_uuids);
        $this->db->where('deleted_at', NULL, FALSE);
        foreach ($this->db->get('perangkat')->result() as $device) {
            if (!empty($device->mapel_uuid)) {
                $mapel_uuids[] = $device->mapel_uuid;
            }
        }

        return array_values(array_unique($mapel_uuids));
    }

    public function get_perangkat_by_mapel($mapel_uuid)
    {
        $this->db->where('mapel_uuid', $mapel_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        apply_admin_creator_scope('guru_uuid');
        $this->db->order_by('jenis_file', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('perangkat')->result();
    }

    public function get_perangkat_by_mapel_and_guru($mapel_uuid, $guru_uuid)
    {
        $this->db->where('mapel_uuid', $mapel_uuid);
        $this->db->where('guru_uuid', $guru_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('jenis_file', 'ASC');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('perangkat')->result();
    }

    public function insert($mapel_uuid, $jenis_file)
    {
        $uuid = Uuid::uuid4()->toString();
        $guru_uuid = $this->session->userdata('uuid');
        $nama_file = $this->input->post('nama_file');
        
        $data = array(
            'uuid' => $uuid,
            'mapel_uuid' => $mapel_uuid,
            'guru_uuid' => $guru_uuid,
            'jenis_file' => $jenis_file,
            'nama_file' => $nama_file,
            'file' => '' // akan diupdate setelah upload
        );

        $this->db->insert('perangkat', $data);
        
        if ($this->db->affected_rows() > 0) {
            return $uuid;
        }
        return false;
    }

    public function update_file($uuid, $file_name)
    {
        $data = array(
            'file' => $file_name,
            'updated_at' => date("Y-m-d H:i:s")
        );
        $this->db->update('perangkat', $data, array('uuid' => $uuid));
        return ($this->db->affected_rows() > 0);
    }

    public function get_by_uuid($uuid)
    {
        $this->db->where('uuid', $uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        apply_admin_creator_scope('guru_uuid');
        return $this->db->get('perangkat')->row();
    }

    public function delete_by_uuid($uuid)
    {
        $data = array(
            'deleted_at' => date("Y-m-d H:i:s")
        );
        $this->db->where('uuid', $uuid);
        apply_admin_creator_scope('guru_uuid');
        $this->db->update('perangkat', $data);
        return ($this->db->affected_rows() > 0);
    }

    public function can_access($mapel_uuid)
    {
        $user_role = user_role();
        
        if ($user_role === 'superadmin') {
            return true;
        }

        if (has_role('admin')) {
            $mapel = $this->db->get_where('mapel', array('uuid' => $mapel_uuid, 'deleted_at' => NULL))->row();
            return $mapel && in_array($mapel_uuid, $this->_get_admin_accessible_mapel_uuids(), true);
        }
        
        // Guru hanya bisa akses mapel yang diampu
        $guru_uuid = $this->session->userdata('uuid');
        $mapel_uuids = $this->guru_model->get_mapel_uuid_list($guru_uuid);
        return in_array($mapel_uuid, $mapel_uuids);
    }
}
?>