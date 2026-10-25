<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class bab_model extends CI_Model {

    public function insert($materi_uuid, $dokumentasi)
    {
        $uuid = Uuid::uuid4()->toString();
        $this->db->trans_begin();
        $week = $this->db->query(
            'SELECT uuid FROM materi WHERE uuid = ? AND deleted_at IS NULL FOR UPDATE',
            array($materi_uuid)
        )->row();
        if (!$week || $this->count_by_materi_uuid($materi_uuid) >= 3) {
            $this->db->trans_rollback();
            return false;
        }

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
		if ($this->db->trans_status() && $this->db->affected_rows() > 0) {
			$this->db->trans_commit();
            return $uuid;
        }
		$this->db->trans_rollback();
        return false;
    }

    public function get_by_materi_uuid($materi_uuid)
    {
        $this->db->where('materi_uuid', $materi_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('id', 'ASC');
        return $this->db->get('bab')->result();
    }

    public function count_by_materi_uuid($materi_uuid)
    {
        $this->db->where('materi_uuid', $materi_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->count_all_results('bab');
    }

    public function get_by_uuid($uuid)
    {
        $this->db->where('uuid', $uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->get('bab')->row();
    }

    public function is_bab_read_by_student($bab_uuid, $siswa_uuid)
    {
        return $this->db
            ->where('bab_uuid', $bab_uuid)
            ->where('siswa_uuid', $siswa_uuid)
            ->count_all_results('bab_selesai_baca') > 0;
    }

    public function mark_bab_as_read($bab_uuid, $siswa_uuid)
    {
        return $this->db->query(
            'INSERT INTO bab_selesai_baca (bab_uuid, siswa_uuid) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE bab_uuid = VALUES(bab_uuid)',
            array($bab_uuid, $siswa_uuid)
        );
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
                'read_completed' => $this->is_bab_read_by_student($item->uuid, $siswa_uuid),
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
        if (!$current_bab || !$this->is_materi_unlocked_for_student($current_bab->materi_uuid, $siswa_uuid)) {
            return false;
        }

        $progress = $this->get_progress_for_materi($current_bab->materi_uuid, $siswa_uuid);
        return !empty($progress[$bab_uuid]['unlocked']);
    }

    public function get_progress_for_mapel($mapel_uuid, $siswa_uuid)
    {
        $this->db->where('mapel_uuid', $mapel_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('semester', 'ASC');
        $this->db->order_by('pekan_ke', 'ASC');
        $this->db->order_by('id', 'ASC');
        $materi = $this->db->get('materi')->result();
        $progress = [];
        $periode = $this->db->where('id', 1)->get('periode_akademik')->row();
        $previous_group_completed = true;
        $current_group = null;
        $current_group_completed = true;

        foreach ($materi as $item) {
            $item_semester = (int) $item->semester;
            $item_week = (int) $item->pekan_ke;
            $group_key = $item_semester . ':' . $item_week;

            if ($current_group !== null && $group_key !== $current_group) {
                $previous_group_completed = $current_group_completed;
                $current_group_completed = true;
            }
            $current_group = $group_key;

            $sub_progress = $this->get_progress_for_materi($item->uuid, $siswa_uuid);
            $read_completed = true;
            $completed = empty($sub_progress);
            foreach ($sub_progress as $state) {
                if (!$state['completed']) {
                    $completed = false;
                }
                if (!$state['read_completed']) {
                    $read_completed = false;
                }
            }
            $current_group_completed = $current_group_completed && $read_completed;

            $progress[$item->uuid] = [
                'unlocked' => false,
                'completed' => $completed,
                'semester' => $item_semester,
                'week' => $item_week
            ];
            $within_period = $periode && $item_week >= 1 && (
                $item_semester < (int) $periode->semester
                || ($item_semester === (int) $periode->semester
                    && $item_week <= (int) $periode->pekan)
            );
            $progress[$item->uuid]['unlocked'] = $within_period && $previous_group_completed;
        }

        foreach ($progress as &$state) {
            unset($state['semester'], $state['week']);
        }
        unset($state);

        return $progress;
    }

    public function is_week_completed_for_student($mapel_uuid, $semester, $week, $siswa_uuid)
    {
        if ($week < 1) {
            return true;
        }

        $materi = $this->db
            ->select('uuid')
            ->where('mapel_uuid', $mapel_uuid)
            ->where('semester', $semester)
            ->where('pekan_ke', $week)
            ->where('deleted_at', NULL, FALSE)
            ->get('materi')
            ->result();
        if (empty($materi)) {
            return true;
        }

        $materi_uuids = array_map(function ($item) {
            return $item->uuid;
        }, $materi);
        $bab = $this->db
            ->select('uuid')
            ->where_in('materi_uuid', $materi_uuids)
            ->where('deleted_at', NULL, FALSE)
            ->get('bab')
            ->result();
        if (empty($bab)) {
            return true;
        }

        $bab_uuids = array_map(function ($item) {
            return $item->uuid;
        }, $bab);
        return $this->db
            ->where('siswa_uuid', $siswa_uuid)
            ->where_in('bab_uuid', $bab_uuids)
            ->count_all_results('bab_selesai_baca') === count($bab_uuids);
    }

    public function is_materi_unlocked_for_student($materi_uuid, $siswa_uuid)
    {
        $materi = $this->db
            ->where('uuid', $materi_uuid)
            ->where('deleted_at', NULL, FALSE)
            ->get('materi')
            ->row();
        if (!$materi || !$this->is_materi_accessible_by_period($materi_uuid)) {
            return false;
        }

        $progress = $this->get_progress_for_mapel($materi->mapel_uuid, $siswa_uuid);
        return !empty($progress[$materi_uuid]['unlocked']);
    }

    public function is_materi_accessible_by_period($materi_uuid)
    {
        $materi = $this->db
            ->select('semester, pekan_ke')
            ->where('uuid', $materi_uuid)
            ->where('deleted_at', NULL, FALSE)
            ->get('materi')
            ->row();
        $periode = $this->db
            ->where('id', 1)
            ->get('periode_akademik')
            ->row();

        if (!$materi || !$periode || (int) $materi->pekan_ke < 1) {
            return false;
        }

        return (int) $materi->semester < (int) $periode->semester
            || ((int) $materi->semester === (int) $periode->semester
                && (int) $materi->pekan_ke <= (int) $periode->pekan);
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