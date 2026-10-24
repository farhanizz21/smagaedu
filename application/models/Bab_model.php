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
        if (!$current_bab || !$this->is_materi_accessible_by_period($current_bab->materi_uuid)) {
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
        $previous_week_completed = $periode
            ? $this->is_week_completed_for_student(
                $mapel_uuid,
                (int) $periode->semester,
                (int) $periode->pekan - 1,
                $siswa_uuid
            )
            : false;

        foreach ($materi as $item) {
            $sub_progress = $this->get_progress_for_materi($item->uuid, $siswa_uuid);
            $completed = empty($sub_progress);
            foreach ($sub_progress as $state) {
                if (!$state['completed']) {
                    $completed = false;
                    break;
                }
            }

            $item_semester = (int) $item->semester;
            $item_week = (int) $item->pekan_ke;
            $progress[$item->uuid] = [
                'unlocked' => false,
                'completed' => $completed,
                'semester' => $item_semester,
                'week' => $item_week
            ];
            $is_past = $periode && (
                $item_semester < (int) $periode->semester
                || ($item_semester === (int) $periode->semester
                    && $item_week < (int) $periode->pekan)
            );
            $is_current = $periode
                && $item_semester === (int) $periode->semester
                && $item_week === (int) $periode->pekan;

            $progress[$item->uuid]['unlocked'] = $is_past
                || ($is_current && $previous_week_completed);
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

        $ujian_uuids = [];
        foreach ($bab as $item) {
            $ujian = $this->db
                ->select('u.uuid')
                ->distinct()
                ->from('ujian u')
                ->join('sub_materi sm', 'sm.uuid = u.sub_materi_uuid AND sm.deleted_at IS NULL', 'left')
                ->where('u.deleted_at', NULL, FALSE)
                ->group_start()
                    ->where('u.bab_uuid', $item->uuid)
                    ->or_where('sm.bab_uuid', $item->uuid)
                ->group_end()
                ->get()
                ->result();

            foreach ($ujian as $exam) {
                $ujian_uuids[$exam->uuid] = true;
            }
        }

        foreach (array_keys($ujian_uuids) as $ujian_uuid) {
            $submitted = $this->db
                ->where('ujian_uuid', $ujian_uuid)
                ->where('created_by', $siswa_uuid)
                ->where('deleted_at', NULL, FALSE)
                ->count_all_results('ujian_jawaban') > 0;

            if (!$submitted) {
                return false;
            }
        }

        return true;
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