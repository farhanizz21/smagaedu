<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class kelompok_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'kelompok',
				'label' => 'Nama Kelompok',
				'rules' => 'required|is_unique[kelompok.kelompok]',
				'errors' => [
					'required' => 'Nama Kelompok wajib diisi.',
					'is_unique' => 'Nama Kelompok sudah digunakan.'
            ]
			]
        ];
	}

	public function get_all()
	{
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('kelompok')->result();

		return $data;
	} 
	
	public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
		$proyek_uuid = $this->input->post('proyek_uuid');
		$kelompok = $this->input->post('kelompok');

		$data = array(
			'uuid' => $uuid,
			'proyek_uuid' => $proyek_uuid,
			'kelompok' => $kelompok,
			'created_by' => $this->session->userdata('uuid')
		);
		$this->db->insert('kelompok', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function insert_siswa_by_kelompok($kelompok_uuid)
	{
		$uuid = Uuid::uuid4()->toString();
		$siswa_uuid = $this->input->post('siswa_uuid');
		
		$data = array(			
			'uuid' => $uuid,
			'siswa_uuid' => $siswa_uuid,
			'kelompok_uuid' => $kelompok_uuid,
			'created_by' => $this->session->userdata('uuid')
		);
		$this->db->insert('kelompok_siswa', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function delete_kelompok_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		return $this->db->delete('kelompok');
	}
	
	public function delete_siswa_kelompok_by_relasi($uuid)
	{
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('kelompok_siswa', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_proyek_uuid($proyek_uuid)
	{	
		$this->db->select('k.kelompok, k.uuid AS kelompok_uuid, ks.uuid AS relasi, s.nama, s.uuid AS siswa_uuid');
		$this->db->from('kelompok k');
		$this->db->join('kelompok_siswa ks', 'ks.kelompok_uuid = k.uuid','left');
		$this->db->join('siswa s', 's.uuid = ks.siswa_uuid','left');
        $this->db->where('k.proyek_uuid', $proyek_uuid);
		$this->db->where('ks.deleted_at', NULL, FALSE);
		$this->db->order_by('k.modified_at', 'ASC');
		
		$query = $this->db->get();
		$result = $query->result_array();
		$data = [];

		foreach ($result as $row) {
			$uuid = $row['kelompok_uuid'];
			if (!isset($data[$uuid])) {
				$data[$uuid] = [
					'kelompok' => $row['kelompok'],
					'kelompok_uuid' => $row['kelompok_uuid'],
					'anggota' => []
				];
			}
			if (!empty($row['siswa_uuid'])) {
				$data[$uuid]['anggota'][] = [
					'relasi' => $row['relasi'],
					'nama' => $row['nama'],
					'siswa_uuid' => $row['siswa_uuid']
				];
			}
		}
		return array_values($data);
	}
	
	public function is_siswa_exist($siswa_uuid, $proyek_uuid)
	{
		$this->db->select('ks.siswa_uuid');
		$this->db->from('kelompok_siswa ks');
		$this->db->join('kelompok k', 'k.uuid = ks.kelompok_uuid', 'left');
		$this->db->where('ks.siswa_uuid', $siswa_uuid);
		$this->db->where('k.proyek_uuid', $proyek_uuid);
		$this->db->where('ks.deleted_at', NULL, FALSE);
		
		$query = $this->db->get();
		
		return $query->num_rows() > 0; 
	}

}
?>