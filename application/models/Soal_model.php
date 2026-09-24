<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class soal_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'soal',
				'label' => 'Soal',
				'rules' => 'required'
			],
			[
				'field' => 'jenis_soal',
				'label' => 'Jenis Soal',
				'rules' => 'required'
			]
		];
	}

	public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
		$soal = $this->input->post('soal');
		$ujian_uuid = $this->input->post('ujian_uuid');
		$user = $this->session->userdata('uuid');

		$jenis_soal = $this->input->post('jenis_soal');
		$jenis_jawaban_essay = $jenis_soal === 'essay'
			? ($this->input->post('jenis_jawaban_essay') === 'file' ? 'file' : 'teks')
			: null;
		$jawaban_benar = $this->input->post('jawaban_benar');
		// Handle array (multiple answer) by encoding to JSON
		if (is_array($jawaban_benar)) {
			$jawaban_benar = json_encode($jawaban_benar);
		}

		$data = [
			'uuid' => $uuid,
			'ujian_uuid' => $ujian_uuid,
			'soal' => $soal,
			'jenis_soal' => $jenis_soal,
			'jenis_jawaban_essay' => $jenis_jawaban_essay,
			'jawaban_a' => $this->input->post('jawaban_a'),
			'jawaban_b' => $this->input->post('jawaban_b'),
			'jawaban_c' => $this->input->post('jawaban_c'),
			'jawaban_d' => $this->input->post('jawaban_d'),
			'jawaban_e' => $this->input->post('jawaban_e'),
			'jawaban_benar' => $jawaban_benar,
			'created_by' => $user
		];
		
		$this->db->insert('ujian_soal', $data);
		if ($this->db->affected_rows() > 0) {
			if ($jenis_soal === 'menjodohkan') {
				$pairs = $this->input->post('jodohkan_pairs');
				if ($pairs && is_array($pairs)) {
					$this->insert_jodohkan_pairs($uuid, $pairs);
				}
			}
			return $uuid; 
		} else {
			return false;
		}
	}

	public function insert_import($soal_data, $jodohkan_pairs = array())
	{
		$uuid = Uuid::uuid4()->toString();
		$user = $this->session->userdata('uuid');

		$data = array_merge(array(
			'uuid' => $uuid,
			'created_by' => $user,
			'modified_at' => date("Y-m-d H:i:s")
		), $soal_data);

		$this->db->insert('ujian_soal', $data);
		if ($this->db->affected_rows() > 0) {
			if (!empty($jodohkan_pairs) && is_array($jodohkan_pairs)) {
				$this->insert_jodohkan_pairs($uuid, $jodohkan_pairs);
			}
			return $uuid;
		}
		return false;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('ujian_soal', $data, array('uuid' => $uuid));
		if ($this->db->affected_rows() > 0) {
			$this->db->update('ujian_soal_jodohkan', $data, array('soal_uuid' => $uuid));
			return true;
		}
		return false;
	}
	
	public function get_by_ujian_uuid($ujian_uuid)
	{
		$this->db->select("soal, uuid, jawaban_benar, jenis_soal, jenis_jawaban_essay, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_e");
		$this->db->where('ujian_uuid', $ujian_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$data = $this->db->get('ujian_soal');

		return $data->result();
	}

	public function get_by_uuid($uuid)
	{
		$this->db->select("soal, uuid, jawaban_benar, jenis_soal, jenis_jawaban_essay, ujian_uuid, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_e");
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$data = $this->db->get('ujian_soal');

		return $data->row();
	}

	public function update($uuid)
	{
		$soal = $this->input->post('soal');
		$jenis_soal = $this->input->post('jenis_soal');
		$jenis_jawaban_essay = $jenis_soal === 'essay'
			? ($this->input->post('jenis_jawaban_essay') === 'file' ? 'file' : 'teks')
			: null;
		$jawaban_benar = $this->input->post('jawaban_benar');

		if (is_array($jawaban_benar)) {
			$jawaban_benar = json_encode($jawaban_benar);
		}

		$data = array(
			'soal' => $soal,
			'jenis_soal' => $jenis_soal,
			'jenis_jawaban_essay' => $jenis_jawaban_essay,
			'jawaban_a' => $this->input->post('jawaban_a'),
			'jawaban_b' => $this->input->post('jawaban_b'),
			'jawaban_c' => $this->input->post('jawaban_c'),
			'jawaban_d' => $this->input->post('jawaban_d'),
			'jawaban_e' => $this->input->post('jawaban_e'),
			'jawaban_benar' => $jawaban_benar,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('ujian_soal', $data, array('uuid' => $uuid));
		$updated = ($this->db->affected_rows() > 0) ? true : false;

		if ($jenis_soal === 'menjodohkan') {
			$pairs = $this->input->post('jodohkan_pairs');
			if ($pairs && is_array($pairs)) {
				$this->delete_jodohkan_pairs($uuid);
				$this->insert_jodohkan_pairs($uuid, $pairs);
			}
		}

		return $updated;
	}

	public function insert_jodohkan_pairs($soal_uuid, $pairs)
	{
		$this->db->delete('ujian_soal_jodohkan', array('soal_uuid' => $soal_uuid));
		$inserted = 0;
		foreach ($pairs as $index => $pair) {
			$kunci = trim($pair['kunci'] ?? '');
			$jawaban = trim($pair['jawaban'] ?? '');
			if ($kunci === '' || $jawaban === '') {
				continue;
			}
			$data = array(
				'uuid' => Uuid::uuid4()->toString(),
				'soal_uuid' => $soal_uuid,
				'kunci' => $kunci,
				'jawaban' => $jawaban,
				'urutan' => $index
			);
			$this->db->insert('ujian_soal_jodohkan', $data);
			$inserted++;
		}
		return $inserted;
	}

	public function get_jodohkan_pairs($soal_uuid)
	{
		$this->db->where('soal_uuid', $soal_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('urutan', 'ASC');
		$query = $this->db->get('ujian_soal_jodohkan');
		return $query->result();
	}

	public function delete_jodohkan_pairs($soal_uuid)
	{
		return $this->db->delete('ujian_soal_jodohkan', array('soal_uuid' => $soal_uuid));
	}


}
?>