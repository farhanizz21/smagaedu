<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;

class Tugas_model extends CI_Model
{
	public function rules()
	{
		return array(
			array('field' => 'judul', 'label' => 'Judul Tugas', 'rules' => 'required|max_length[100]'),
			array('field' => 'tgl_mulai', 'label' => 'Tanggal Mulai', 'rules' => 'required'),
			array('field' => 'tgl_selesai', 'label' => 'Tanggal Selesai', 'rules' => 'required')
		);
	}

	public function get_all($limit = NULL, $offset = 0)
	{
		$this->db->select("t.*, m.nama AS mapel_nama, g.nama AS guru_nama, b.judul AS pertemuan_judul, DATE_FORMAT(t.tgl_mulai, '%d %M %Y %H:%i') AS tgl_mulai_formatted, DATE_FORMAT(t.tgl_selesai, '%d %M %Y %H:%i') AS tgl_selesai_formatted", FALSE);
		$this->db->from('tugas t');
		$this->db->join('mapel m', 'm.uuid = t.mapel_uuid', 'left');
		$this->db->join('users g', 'g.uuid = t.created_by', 'left');
		$this->db->join('bab b', 'b.uuid = t.bab_uuid', 'left');
		$this->db->where('t.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('t.created_by');
		$this->db->order_by('t.tgl_mulai', 'DESC');
		if ($limit !== NULL) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		return $this->db->get()->result();
	}

	public function count_all()
	{
		$this->db->from('tugas t');
		$this->db->where('t.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('t.created_by');
		return $this->db->count_all_results();
	}

	public function get_by_mapel_uuids($mapel_uuids, $limit = NULL, $offset = 0)
	{
		if (empty($mapel_uuids)) {
			return array();
		}
		$this->db->select("t.*, m.nama AS mapel_nama, g.nama AS guru_nama, b.judul AS pertemuan_judul, DATE_FORMAT(t.tgl_mulai, '%d %M %Y %H:%i') AS tgl_mulai_formatted, DATE_FORMAT(t.tgl_selesai, '%d %M %Y %H:%i') AS tgl_selesai_formatted", FALSE);
		$this->db->from('tugas t');
		$this->db->join('mapel m', 'm.uuid = t.mapel_uuid', 'left');
		$this->db->join('users g', 'g.uuid = t.created_by', 'left');
		$this->db->join('bab b', 'b.uuid = t.bab_uuid', 'left');
		$this->db->where('t.deleted_at', NULL, FALSE);
		$this->db->where_in('t.mapel_uuid', $mapel_uuids);
		$this->db->order_by('t.tgl_mulai', 'DESC');
		if ($limit !== NULL) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		return $this->db->get()->result();
	}

	public function get_by_bab($bab_uuid)
	{
		$this->db->select("t.*, m.nama AS mapel_nama, g.nama AS guru_nama, DATE_FORMAT(t.tgl_mulai, '%H.%i WIB, %d %M %Y') AS tgl_mulai_formatted, DATE_FORMAT(t.tgl_selesai, '%H.%i WIB, %d %M %Y') AS tgl_selesai_formatted", FALSE);
		$this->db->from('tugas t');
		$this->db->join('mapel m', 'm.uuid = t.mapel_uuid', 'left');
		$this->db->join('users g', 'g.uuid = t.created_by', 'left');
		$this->db->where('t.bab_uuid', $bab_uuid);
		$this->db->where('t.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('t.created_by');
		$this->db->order_by('t.modified_at', 'DESC');
		return $this->db->get()->result();
	}

	public function get_pending_by_mapel_uuids($mapel_uuids, $siswa_uuid, $limit = 5)
	{
		if (empty($mapel_uuids)) {
			return array();
		}
		$this->db->select('t.uuid, t.judul, t.tgl_selesai, t.modified_at, m.nama AS mapel_nama');
		$this->db->from('tugas t');
		$this->db->join('mapel m', 'm.uuid = t.mapel_uuid', 'left');
		$this->db->where('t.deleted_at', NULL, FALSE);
		$this->db->where_in('t.mapel_uuid', $mapel_uuids);
		$this->db->where(
			'NOT EXISTS (
				SELECT 1 FROM tugas_jawaban tj
				WHERE tj.tugas_uuid = t.uuid
					AND tj.created_by = ' . $this->db->escape($siswa_uuid) . '
					AND tj.deleted_at IS NULL
					AND tj.jawaban_siswa != \'\'
			)',
			NULL,
			FALSE
		);
		$this->db->order_by('t.modified_at', 'DESC');
		$this->db->limit((int) $limit);
		return $this->db->get()->result();
	}

	public function count_by_mapel_uuids($mapel_uuids)
	{
		if (empty($mapel_uuids)) {
			return 0;
		}
		$this->db->from('tugas t');
		$this->db->where('t.deleted_at', NULL, FALSE);
		$this->db->where_in('t.mapel_uuid', $mapel_uuids);
		return $this->db->count_all_results();
	}

	public function get_by_uuid($uuid)
	{
		$this->db->select('t.*, m.nama AS mapel_nama, g.nama AS guru_nama, b.judul AS pertemuan_judul');
		$this->db->from('tugas t');
		$this->db->join('mapel m', 'm.uuid = t.mapel_uuid', 'left');
		$this->db->join('users g', 'g.uuid = t.created_by', 'left');
		$this->db->join('bab b', 'b.uuid = t.bab_uuid', 'left');
		$this->db->where('t.uuid', $uuid);
		$this->db->where('t.deleted_at', NULL, FALSE);
		return $this->db->get()->row();
	}

	public function insert($mapel_uuid, $bab_uuid = NULL)
	{
		$start = DateTime::createFromFormat('!Y-m-d\TH:i', (string) $this->input->post('tgl_mulai'));
		$end = DateTime::createFromFormat('!Y-m-d\TH:i', (string) $this->input->post('tgl_selesai'));
		if (!$start || !$end || $end <= $start) {
			return FALSE;
		}
		$data = array(
			'uuid' => Uuid::uuid4()->toString(),
			'mapel_uuid' => $mapel_uuid,
			'bab_uuid' => $bab_uuid,
			'judul' => $this->input->post('judul', TRUE),
			'tgl_mulai' => $start->format('Y-m-d H:i:s'),
			'tgl_selesai' => $end->format('Y-m-d H:i:s'),
			'created_by' => $this->session->userdata('uuid')
		);
		return $this->db->insert('tugas', $data) ? $data['uuid'] : FALSE;
	}

	public function update($uuid, $mapel_uuid)
	{
		$start = DateTime::createFromFormat('!Y-m-d\TH:i', (string) $this->input->post('tgl_mulai'));
		$end = DateTime::createFromFormat('!Y-m-d\TH:i', (string) $this->input->post('tgl_selesai'));
		if (!$start || !$end || $end <= $start) {
			return FALSE;
		}
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		return $this->db->update('tugas', array(
			'mapel_uuid' => $mapel_uuid,
			'judul' => $this->input->post('judul', TRUE),
			'tgl_mulai' => $start->format('Y-m-d H:i:s'),
			'tgl_selesai' => $end->format('Y-m-d H:i:s'),
			'modified_at' => date('Y-m-d H:i:s')
		));
	}

	public function has_student_submissions($tugas_uuid)
	{
		return $this->db
			->where('tugas_uuid', $tugas_uuid)
			->where('deleted_at', NULL, FALSE)
			->where('jawaban_siswa !=', '')
			->count_all_results('tugas_jawaban') > 0;
	}

	public function get_submission_answers($tugas_uuid)
	{
		$this->db->select('tj.created_by AS siswa_uuid, siswa.nama AS siswa_nama, siswa.nis, kelas.nama AS kelas_nama, tj.modified_at AS jawaban_diubah, tj.jawaban_siswa, soal.soal, soal.jenis_soal, soal.jenis_jawaban_essay');
		$this->db->from('tugas_jawaban tj');
		$this->db->join('siswa', 'siswa.uuid = tj.created_by', 'left');
		$this->db->join('kelas', 'kelas.uuid = siswa.kelas_uuid', 'left');
		$this->db->join('ujian_soal soal', 'soal.uuid = tj.soal_uuid', 'inner');
		$this->db->where('tj.tugas_uuid', $tugas_uuid);
		$this->db->where('tj.deleted_at', NULL, FALSE);
		$this->db->where('tj.jawaban_siswa !=', '');
		$this->db->order_by('siswa.nama', 'ASC');
		$this->db->order_by('soal.id', 'ASC');
		return $this->db->get()->result();
	}

	public function get_answers($tugas_uuid, $siswa_uuid)
	{
		$this->db->select('tj.soal_uuid, tj.jawaban_siswa, s.jenis_soal');
		$this->db->from('tugas_jawaban tj');
		$this->db->join('ujian_soal s', 's.uuid = tj.soal_uuid', 'inner');
		$this->db->where('tj.tugas_uuid', $tugas_uuid);
		$this->db->where('tj.created_by', $siswa_uuid);
		$this->db->where('tj.deleted_at', NULL, FALSE);
		$answers = array();
		foreach ($this->db->get()->result() as $answer) {
			$decoded = json_decode($answer->jawaban_siswa, TRUE);
			$answers[$answer->soal_uuid] = in_array($answer->jenis_soal, array('pilihan_ganda_kompleks', 'menjodohkan'), TRUE)
				&& json_last_error() === JSON_ERROR_NONE && is_array($decoded)
				? $decoded
				: $answer->jawaban_siswa;
		}
		return $answers;
	}

	public function has_submission($tugas_uuid, $siswa_uuid)
	{
		return $this->db
			->where('tugas_uuid', $tugas_uuid)
			->where('created_by', $siswa_uuid)
			->where('deleted_at', NULL)
			->where('jawaban_siswa !=', '')
			->count_all_results('tugas_jawaban') > 0;
	}

	public function save_answers($tugas_uuid, $siswa_uuid, $answers)
	{
		if (!is_array($answers)) {
			return FALSE;
		}
		$this->db->select('uuid, jenis_soal, jenis_jawaban_essay');
		$this->db->where('tugas_uuid', $tugas_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$valid_questions = array();
		foreach ($this->db->get('ujian_soal')->result() as $question) {
			$valid_questions[$question->uuid] = $question;
		}

		$this->db->trans_start();
		foreach ($valid_questions as $soal_uuid => $question) {
			if (!array_key_exists($soal_uuid, $answers)) {
				if ($question->jenis_soal === 'essay' && $question->jenis_jawaban_essay === 'file') {
					continue;
				}
				$answer_text = '';
			} else {
				$answer = $answers[$soal_uuid];
				if (is_array($answer)) {
					$answer = array_filter($answer, function ($value) {
						return !is_array($value) && trim((string) $value) !== '';
					});
					$answer_text = empty($answer) ? '' : json_encode($answer);
				} else {
					$answer_text = trim((string) $answer);
				}
			}
			$existing = $this->db->get_where('tugas_jawaban', array(
				'tugas_uuid' => $tugas_uuid,
				'soal_uuid' => $soal_uuid,
				'created_by' => $siswa_uuid
			))->row();
			if ($answer_text === '') {
				if ($existing) {
					$this->db->where('uuid', $existing->uuid)->update('tugas_jawaban', array(
						'deleted_at' => date('Y-m-d H:i:s')
					));
				}
				continue;
			}
			if ($answer_text === FALSE) {
				$this->db->trans_rollback();
				return FALSE;
			}
			if ($existing) {
				$this->db->where('uuid', $existing->uuid)->update('tugas_jawaban', array(
					'jawaban_siswa' => $answer_text,
					'modified_at' => date('Y-m-d H:i:s'),
					'deleted_at' => NULL
				));
			} else {
				$this->db->insert('tugas_jawaban', array(
					'uuid' => Uuid::uuid4()->toString(),
					'tugas_uuid' => $tugas_uuid,
					'soal_uuid' => $soal_uuid,
					'jawaban_siswa' => $answer_text,
					'created_by' => $siswa_uuid
				));
			}
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function delete_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->update('tugas', array('deleted_at' => date('Y-m-d H:i:s')));
		return $this->db->affected_rows() > 0;
	}
}
