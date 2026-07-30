<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class proyek_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'judul',
				'label' => 'Judul proyek',
				'rules' => 'required'
			],
			[
				'field' => 'tgl_mulai',
				'label' => 'Tanggal Mulai',
				'rules' => 'required'
			],
			[
				'field' => 'tgl_selesai',
				'label' => 'Tanggal Selesai',
				'rules' => 'required'
			],
			// [
			// 	'field' => 'berkas',
			// 	'label' => 'Berkas',
			// 	'rules' => 'uploaded[berkas]|max_size[berkas,5120]' // 5MB
			// ],
			[
				'field' => 'namaMapel',
				'label' => 'Mata Pelajaran',
				'rules' => 'required'
			]
        ];
	}

	public function get_all($created_by = null)
	{
		$this->db->where('deleted_at', NULL, FALSE);
		if ($created_by !== null) {
			$this->db->where('created_by', $created_by);
		}
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('proyek')->result();

		return $data;
	} 
	
	public function insert($berkas)
	{
		$uuid = Uuid::uuid4()->toString();
        $judul = $this->input->post('judul');
        $mapel_uuid = $this->input->post('namaMapel');
        $deskripsi = $this->input->post('deskripsi');
        $tgl_selesai = $this->input->post('tgl_selesai');
        $tgl_mulai = $this->input->post('tgl_mulai');
        $user_uuid = $this->session->userdata('uuid');


		$data = array(
			'uuid' => $uuid,
			'judul' => $judul,
			'mapel_uuid' => $mapel_uuid,
			'file' => $berkas,
			'deskripsi' => $deskripsi,
			'tgl_mulai' => $tgl_mulai,
			'tgl_selesai' => $tgl_selesai,
			'created_by' => $user_uuid
		);

		$this->db->insert('proyek', $data);
		$this->db->last_query();  // Menampilkan query terakhir yang dijalankan

		
		if ($this->db->affected_rows() > 0) {
			return $uuid; 
		} else {
			return false;
		}
	}

	public function get_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		$data = $this->db->get('proyek')->row();

		return $data;
	}

	public function get_komentar_by_proyek_uuid($uuid)
	{
		$this->db->where('proyek_uuid', $uuid);
		$data = $this->db->get('komentar')->row();

		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('proyek', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	
}
?>