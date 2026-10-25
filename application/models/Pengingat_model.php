<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengingat_model extends CI_Model {

	public function get_for_user($role, $user_uuid, $periode)
	{
		if ($role === 'guru') {
			return $this->get_teacher_reminders($user_uuid, $periode);
		}

		if ($role === 'siswa') {
			return $this->get_student_reminders($user_uuid, $periode);
		}

		if ($role === 'kepala_sekolah') {
			return $this->get_principal_reminders();
		}

		return array();
	}

	private function get_teacher_reminders($guru_uuid, $periode)
	{
		if (!$periode) {
			return array();
		}

		$this->load->model('guru_model');
		$this->load->model('mapel_model');
		$mapel_uuids = $this->guru_model->get_mapel_uuid_list($guru_uuid);
		if (empty($mapel_uuids)) {
			return array();
		}

		$mapel_list = $this->mapel_model->get_many_mapel_by_uuid($mapel_uuids);
		$mapel_names = array();
		foreach ($mapel_list as $mapel) {
			$mapel_names[$mapel->uuid] = $mapel->nama;
		}

		$semester = (int) $periode->semester;
		$current_week = (int) $periode->pekan;
		$week_limits = array_fill_keys($mapel_uuids, 20);
		if ($semester === 2) {
			$mapel_kelas_xii = $this->guru_model->get_mapel_uuids_for_kelas_tingkat('XII');
			foreach ($mapel_uuids as $mapel_uuid) {
				if (in_array($mapel_uuid, $mapel_kelas_xii, TRUE)) {
					$week_limits[$mapel_uuid] = 10;
				}
			}
		}

		$target_weeks = array_values(array_filter(
			array($current_week, $current_week + 1),
			function ($week) {
				return $week <= 20;
			}
		));
		if (empty($target_weeks)) {
			return array();
		}

		$this->db->select(
			'm.uuid, m.mapel_uuid, m.pekan_ke, m.semester, m.modified_at, ' .
			'(SELECT COUNT(*) FROM bab b WHERE b.materi_uuid = m.uuid AND b.deleted_at IS NULL) AS meeting_count',
			FALSE
		);
		$this->db->from('materi m');
		$this->db->where('m.deleted_at', NULL, FALSE);
		$this->db->where_in('m.mapel_uuid', $mapel_uuids);
		$this->db->where('m.semester', $semester);
		$this->db->where_in('m.pekan_ke', $target_weeks);
		$this->db->order_by('m.modified_at', 'DESC');
		$materi_rows = $this->db->get()->result();

		$materi_by_week = array();
		foreach ($materi_rows as $materi) {
			$key = $materi->mapel_uuid . ':' . (int) $materi->pekan_ke;
			if (!isset($materi_by_week[$key])) {
				$materi_by_week[$key] = $materi;
			}
		}

		$reminders = array();
		foreach ($mapel_uuids as $mapel_uuid) {
			if (!isset($mapel_names[$mapel_uuid])) {
				continue;
			}

			foreach ($target_weeks as $week) {
				if ($week > $week_limits[$mapel_uuid]) {
					continue;
				}

				$key = $mapel_uuid . ':' . $week;
				$meeting_count = isset($materi_by_week[$key])
					? (int) $materi_by_week[$key]->meeting_count
					: 0;
				if ($meeting_count >= 3) {
					continue;
				}

				$is_current_week = $week === $current_week;
				$week_url = isset($materi_by_week[$key])
					? base_url('sub_bab/index/' . rawurlencode($materi_by_week[$key]->uuid))
					: base_url('bab/tambah/' . rawurlencode($mapel_uuid) .
						'?semester=' . $semester . '&pekan_ke=' . $week);
				$reminders[] = array(
					'title' => $is_current_week ? 'Pekan berjalan belum lengkap' : 'Persiapkan pekan berikutnya',
					'message' => 'Pekan ' . $week . ' · ' . $mapel_names[$mapel_uuid] .
						' (' . $meeting_count . '/3 materi atau pertemuan diunggah)',
					'url' => $week_url,
					'label' => 'Lengkapi materi',
					'urgent' => $is_current_week
				);
			}
		}

		usort($reminders, function ($left, $right) {
			return (int) $right['urgent'] <=> (int) $left['urgent'];
		});

		return array_slice($reminders, 0, 5);
	}

	private function get_student_reminders($siswa_uuid, $periode)
	{
		$this->load->model('siswa_model');
		$this->load->model('mapel_model');
		$reminders = array();

		$siswa = $this->siswa_model->get_by_uuid($siswa_uuid, FALSE);
		if ($periode && $siswa && !empty($siswa->kelas_uuid)) {
			$mapel_uuids = $this->mapel_model->get_mapel_uuids_by_kelas($siswa->kelas_uuid);
		} else {
			$mapel_uuids = array();
		}

		if (!empty($mapel_uuids)) {
			$this->db->select('m.pekan_ke, m.mapel_uuid, mp.nama AS mapel_nama', FALSE);
			$this->db->from('bab b');
			$this->db->join('materi m', 'm.uuid = b.materi_uuid AND m.deleted_at IS NULL', 'inner');
			$this->db->join('mapel mp', 'mp.uuid = m.mapel_uuid', 'inner');
			$this->db->join(
				'bab_selesai_baca bsb',
				'bsb.bab_uuid = b.uuid AND bsb.siswa_uuid = ' . $this->db->escape($siswa_uuid),
				'left',
				FALSE
			);
			$this->db->where('b.deleted_at', NULL, FALSE);
			$this->db->where('bsb.bab_uuid IS NULL', NULL, FALSE);
			$this->db->where_in('m.mapel_uuid', $mapel_uuids);
			$this->db->where('m.semester', (int) $periode->semester);
			$this->db->where('m.pekan_ke', (int) $periode->pekan);
			$this->db->order_by('mp.nama', 'ASC');
			$this->db->group_by(array('m.mapel_uuid', 'm.pekan_ke', 'mp.nama'));
			$this->db->limit(3);
			$unread_materials = $this->db->get()->result();

			foreach ($unread_materials as $material) {
				$reminders[] = array(
					'title' => 'Materi pekan ini belum dibaca',
					'message' => 'Pekan ' . (int) $material->pekan_ke . ' · ' . $material->mapel_nama,
					'url' => base_url('mata_pelajaran/semester/' . rawurlencode($material->mapel_uuid) .
						'?semester=' . (int) $periode->semester),
					'label' => 'Baca materi',
					'urgent' => TRUE
				);
			}
		}

		$this->db->select('u.uuid, u.nama, u.tgl_selesai, mp.nama AS mapel_nama');
		$this->db->from('ujian_siswa us');
		$this->db->join('ujian u', 'u.uuid = us.ujian_uuid', 'inner');
		$this->db->join('mapel mp', 'mp.uuid = u.mapel_uuid', 'left');
		$this->db->where('us.siswa_uuid', $siswa_uuid);
		$this->db->where('us.deleted_at', NULL, FALSE);
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->where('u.tgl_selesai >=', date('Y-m-d H:i:s'));
		$this->db->where(
			'NOT EXISTS (
				SELECT 1 FROM ujian_jawaban uj
				WHERE uj.ujian_uuid = u.uuid
				AND uj.created_by = ' . $this->db->escape($siswa_uuid) . '
				AND uj.deleted_at IS NULL
			)',
			NULL,
			FALSE
		);
		$this->db->order_by('u.tgl_selesai', 'ASC');
		$this->db->limit(3);
		$unfinished_exams = $this->db->get()->result();

		foreach ($unfinished_exams as $exam) {
			$deadline = strtotime($exam->tgl_selesai);
			$reminders[] = array(
				'title' => 'Ujian belum dikerjakan',
				'message' => $exam->nama . (!empty($exam->mapel_nama) ? ' · ' . $exam->mapel_nama : '') .
					($deadline ? ' · Batas ' . date('d M Y H:i', $deadline) : ''),
				'url' => base_url('ujian'),
				'label' => 'Lihat ujian',
				'urgent' => TRUE
			);
		}

		return $reminders;
	}

	private function get_principal_reminders()
	{
		$this->load->model('guru_model');
		$teachers = $this->guru_model->get_all();
		if (empty($teachers)) {
			return array();
		}

		$mapel_uuids = array();
		$teacher_uuids = array();
		foreach ($teachers as $teacher) {
			$teacher_uuids[] = $teacher->uuid;
			foreach ($teacher->mapel_data as $mapel) {
				$mapel_uuids[$mapel->uuid] = TRUE;
			}
		}

		$latest_by_mapel = array(
			'materi' => $this->latest_mapel_activity('materi', $mapel_uuids),
			'bab' => $this->latest_mapel_activity('bab', $mapel_uuids),
			'sub_materi' => $this->latest_mapel_activity('sub_materi', $mapel_uuids)
		);
		$latest_exam_by_teacher = $this->latest_exam_activity($teacher_uuids);

		foreach ($teachers as $teacher) {
			$latest = array();
			foreach ($teacher->mapel_data as $mapel) {
				foreach ($latest_by_mapel as $activity_by_mapel) {
					if (!empty($activity_by_mapel[$mapel->uuid])) {
						$latest[] = $activity_by_mapel[$mapel->uuid];
					}
				}
				if (!empty($latest_exam_by_teacher[$teacher->uuid][$mapel->uuid])) {
					$latest[] = $latest_exam_by_teacher[$teacher->uuid][$mapel->uuid];
				}
			}

			$teacher->reminder_last_update = empty($latest) ? NULL : max($latest);
		}

		usort($teachers, function ($left, $right) {
			$left_update = $left->reminder_last_update ? strtotime($left->reminder_last_update) : 0;
			$right_update = $right->reminder_last_update ? strtotime($right->reminder_last_update) : 0;
			return $right_update - $left_update;
		});
		$teachers = array_slice($teachers, max(0, count($teachers) - 3));

		$reminders = array();
		foreach ($teachers as $teacher) {
			$last_update = $teacher->reminder_last_update
				? 'Pembaruan terakhir ' . date('d M Y', strtotime($teacher->reminder_last_update))
				: 'Belum ada materi atau ujian yang diunggah';
			$reminders[] = array(
				'title' => 'Guru perlu perhatian: ' . $teacher->nama,
				'message' => $last_update,
				'url' => base_url('kepala_sekolah/detail/' . rawurlencode($teacher->uuid)),
				'label' => 'Lihat guru',
				'urgent' => empty($teacher->reminder_last_update)
			);
		}

		return $reminders;
	}

	private function latest_mapel_activity($activity, $mapel_uuids)
	{
		if (empty($mapel_uuids)) {
			return array();
		}

		$this->db->select('materi.mapel_uuid, MAX(' . $activity . '.modified_at) AS latest_update', FALSE);
		if ($activity === 'materi') {
			$this->db->from('materi');
		} elseif ($activity === 'bab') {
			$this->db->from('bab');
			$this->db->join('materi', 'materi.uuid = bab.materi_uuid', 'inner');
		} else {
			$this->db->from('sub_materi');
			$this->db->join('bab', 'bab.uuid = sub_materi.bab_uuid', 'inner');
			$this->db->join('materi', 'materi.uuid = bab.materi_uuid', 'inner');
		}
		$this->db->where($activity . '.deleted_at', NULL, FALSE);
		if ($activity !== 'materi') {
			$this->db->where('bab.deleted_at', NULL, FALSE);
			$this->db->where('materi.deleted_at', NULL, FALSE);
		}
		$this->db->where_in('materi.mapel_uuid', array_keys($mapel_uuids));
		$this->db->group_by('materi.mapel_uuid');

		$latest = array();
		foreach ($this->db->get()->result() as $row) {
			$latest[$row->mapel_uuid] = $row->latest_update;
		}
		return $latest;
	}

	private function latest_exam_activity($teacher_uuids)
	{
		$this->db->select('created_by, mapel_uuid, MAX(modified_at) AS latest_update', FALSE);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('created_by', $teacher_uuids);
		$this->db->group_by(array('created_by', 'mapel_uuid'));

		$latest = array();
		foreach ($this->db->get('ujian')->result() as $row) {
			$latest[$row->created_by][$row->mapel_uuid] = $row->latest_update;
		}
		return $latest;
	}
}
