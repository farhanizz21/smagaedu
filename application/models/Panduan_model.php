<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class panduan_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'judul',
				'label' => 'Judul',
				'rules' => 'required'
			],
			[
				'field' => 'berkas',
				'label' => 'Berkas Panduan',
				'rules' => 'uploaded[berkas]|max_size[berkas,5120]' // 5MB
			],
			[
				'field' => 'tujuan[]',
				'label' => 'Tujuan',
				'rules' => 'required'
			]
        ];
	}

    public function insert($berkas)
	{
		$uuid = Uuid::uuid4()->toString();
        $judul = $this->input->post('judul');
        $tujuan = $this->input->post('tujuan');
        $tujuan_json = is_array($tujuan) ? json_encode($tujuan) : $tujuan;

		$data = array(
			'uuid' => $uuid,
			'judul' => $judul,
			'berkas' => $berkas,
			'tujuan' => $tujuan_json
		);

		$this->db->insert('panduan', $data);
		$this->db->last_query();  // Menampilkan query terakhir yang dijalankan

		
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_all()
	{
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('panduan')->result();

		return $data;
	}

	public function get_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		return $this->db->get('panduan')->row();
	}

	public function update($uuid)
	{
		$judul = $this->input->post('judul');
		$tujuan = $this->input->post('tujuan');
		$tujuan_json = is_array($tujuan) ? json_encode($tujuan) : $tujuan;

		$data = array(
			'judul' => $judul,
			'tujuan' => $tujuan_json
		);

		// If new file uploaded
		if (!empty($_FILES['berkas']['name'])) {
			$config = array(
				'upload_path' => "./uploads/panduan/",
				'allowed_types' => "jpg|png|jpeg|pdf",
				'overwrite' => TRUE,
				'max_size' => "5120",
				'encrypt_name' => TRUE
			);
			$this->load->library('upload', $config);
			
			if ($this->upload->do_upload('berkas')) {
				$upload_data = $this->upload->data();
				$data['berkas'] = $upload_data['file_name'];
			}
		}

		$this->db->where('uuid', $uuid);
		$this->db->update('panduan', $data);

		return $this->db->affected_rows() > 0;
	}

	public function delete_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		return $this->db->delete('panduan');
	}
}
?>