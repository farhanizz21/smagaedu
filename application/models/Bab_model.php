<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class bab_model extends CI_Model {

    public function insert($materi_uuid, $dokumentasi)
    {
        $uuid = Uuid::uuid4()->toString();
        $data = array(
            'uuid'        => $uuid,
            'materi_uuid' => $materi_uuid,
            'judul'       => $this->input->post('judul'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'dokumentasi' => $dokumentasi,
            'dokumentasi_link' => $this->input->post('dokumentasi_link'),
            'created_by'  => $this->session->userdata('uuid')
        );
        $this->db->insert('bab', $data);
        if ($this->db->affected_rows() > 0) {
            return $uuid;
        }
        return false;
    }

    public function get_by_materi_uuid($materi_uuid)
    {
        $this->db->where('materi_uuid', $materi_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('modified_at', 'ASC');
        return $this->db->get('bab')->result();
    }

    public function get_by_uuid($uuid)
    {
        $this->db->where('uuid', $uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->get('bab')->row();
    }

    public function get_progress_for_materi($materi_uuid, $siswa_uuid)
    {
        $progress = [];
        $previous_completed = true;

        foreach ($this->get_by_materi_uuid($materi_uuid) as $item) {
            $ujian = $this->db
                ->select('u.uuid')
                ->from('ujian u')
                ->join('sub_materi sm', 'sm.uuid = u.sub_materi_uuid AND sm.deleted_at IS NULL', 'left')
                ->where('u.deleted_at', NULL, FALSE)
                ->group_start()
                    ->where('u.bab_uuid', $item->uuid)
                    ->or_where('sm.bab_uuid', $item->uuid)
                ->group_end()
                ->get()
                ->result();

            $completed_count = 0;
            foreach ($ujian as $exam) {
                $submitted = $this->db
                    ->where('ujian_uuid', $exam->uuid)
                    ->where('created_by', $siswa_uuid)
                    ->where('deleted_at', NULL, FALSE)
                    ->count_all_results('ujian_jawaban') > 0;

                if ($submitted) {
                    $completed_count++;
                }
            }

            $activity_count = count($ujian);
            $completed = $activity_count === 0 || $completed_count === $activity_count;
            $progress[$item->uuid] = [
                'unlocked' => $previous_completed,
                'completed' => $completed,
                'activity_count' => $activity_count,
                'completed_count' => $completed_count
            ];
            $previous_completed = $completed;
        }

        return $progress;
    }

    public function is_exam_unlocked_for_student($ujian, $siswa_uuid)
    {
        if (!$ujian->bab_uuid && !$ujian->sub_materi_uuid) {
            return true;
        }

        $bab_uuid = $ujian->bab_uuid;
        if (!$bab_uuid && $ujian->sub_materi_uuid) {
            $sub_materi = $this->db
                ->select('bab_uuid')
                ->where('uuid', $ujian->sub_materi_uuid)
                ->where('deleted_at', NULL, FALSE)
                ->get('sub_materi')
                ->row();
            $bab_uuid = $sub_materi ? $sub_materi->bab_uuid : null;
        }

        $current_bab = $bab_uuid ? $this->get_by_uuid($bab_uuid) : null;
        if (!$current_bab) {
            return false;
        }

        $progress = $this->get_progress_for_materi($current_bab->materi_uuid, $siswa_uuid);
        return !empty($progress[$bab_uuid]['unlocked']);
    }

    public function get_progress_for_mapel($mapel_uuid, $siswa_uuid)
    {
        $this->db->where('mapel_uuid', $mapel_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('modified_at', 'ASC');
        $materi = $this->db->get('materi')->result();
        $progress = [];
        $previous_completed = true;

        foreach ($materi as $item) {
            $sub_progress = $this->get_progress_for_materi($item->uuid, $siswa_uuid);
            $completed = empty($sub_progress);
            foreach ($sub_progress as $state) {
                if (!$state['completed']) {
                    $completed = false;
                    break;
                }
            }

            $progress[$item->uuid] = [
                'unlocked' => $previous_completed,
                'completed' => $completed
            ];
            $previous_completed = $completed;
        }

        return $progress;
    }

    public function is_materi_unlocked_for_student($materi_uuid, $siswa_uuid)
    {
        $materi = $this->db
            ->where('uuid', $materi_uuid)
            ->where('deleted_at', NULL, FALSE)
            ->get('materi')
            ->row();
        if (!$materi) {
            return false;
        }

        $progress = $this->get_progress_for_mapel($materi->mapel_uuid, $siswa_uuid);
        return !empty($progress[$materi_uuid]['unlocked']);
    }

    public function update($uuid, $dokumentasi)
    {
        $data = array(
            'judul'       => $this->input->post('judul'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'dokumentasi' => $dokumentasi,
            'dokumentasi_link' => $this->input->post('dokumentasi_link'),
            'modified_at' => date("Y-m-d H:i:s")
        );
        $this->db->update('bab', $data, array('uuid' => $uuid));
        return ($this->db->affected_rows() > 0);
    }

    public function delete_by_uuid($uuid)
    {
        $data = array('deleted_at' => date("Y-m-d H:i:s"));
        $this->db->update('bab', $data, array('uuid' => $uuid));
        return ($this->db->affected_rows() > 0);
    }
}
?>