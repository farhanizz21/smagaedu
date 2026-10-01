<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class Perangkat_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('guru_model');
    }

    /**
     * Parse mapel_uuid field - handles both old format (simple array) and new format (array of objects with kelas_list)
     */
    private function parse_mapel_uuids($mapel_uuid_json)
    {
        if (empty($mapel_uuid_json)) {
            return [];
        }

        $data = json_decode($mapel_uuid_json, true);
        if (!is_array($data)) {
            return [];
        }

        // Check if old format (simple array of mapel UUIDs)
        if (isset($data[0]) && is_string($data[0])) {
            return $data;
        }

        // New format: [{"mapel_uuid": "uuid1", "kelas_list": [...]}]
        $mapel_list = [];
        foreach ($data as $item) {
            if (isset($item['mapel_uuid'])) {
                $mapel_list[] = $item['mapel_uuid'];
            }
        }
        return $mapel_list;
    }

    public function get_mapel_by_guru($guru_uuid)
    {
        $this->db->select('user_profiles.mapel_uuid');
        $this->db->from('users');
        $this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
        $this->db->where('users.uuid', $guru_uuid);
        $this->db->where('users.role_id', 3);
        $this->db->where('users.deleted_at', NULL, FALSE);
        $guru = $this->db->get()->row();
        
        if (!$guru || empty($guru->mapel_uuid)) {
            return [];
        }
        
        $uuid_array = $this->parse_mapel_uuids($guru->mapel_uuid);
        if (empty($uuid_array)) {
            return [];
        }
        
        $this->db->where_in('uuid', $uuid_array);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('mapel')->result();
    }

    public function get_all_mapel()
    {
        if (user_role() === 'admin') {
            $mapel_uuids = $this->_get_admin_accessible_mapel_uuids();
            if (empty($mapel_uuids)) {
                return [];
            }

            $this->db->where_in('uuid', $mapel_uuids);
        }
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('mapel')->result();
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
        $this->db->where('role_id', 3);
        $this->db->where('created_by', $this->session->userdata('uuid'));
        $this->db->where('deleted_at', NULL, FALSE);
        foreach ($this->db->get('users')->result() as $teacher) {
            foreach ($this->get_mapel_by_guru($teacher->uuid) as $mapel) {
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
        $user_role = $this->session->userdata('role');
        
        if ($user_role === 'superadmin') {
            return true;
        }

        if ($user_role === 'admin') {
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