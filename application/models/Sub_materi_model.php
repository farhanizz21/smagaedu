<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class sub_materi_model extends CI_Model {

    public function rules()
	{
		return [
			[
				'field' => 'judul',
				'label' => 'Judul Sub Bab',
				'rules' => 'required'
			]
		];
	}

	public function insert($bab_uuid, $berkas)
	{
		$uuid = Uuid::uuid4()->toString();
		$data = array(
			'uuid'      => $uuid,
			'bab_uuid'  => $bab_uuid,
			'judul'     => $this->input->post('judul'),
			'berkas'    => $berkas,
			'created_by'=> $this->session->userdata('uuid')
		);
		$this->db->insert('sub_materi', $data);
		return ($this->db->affected_rows() > 0);
	}

	public function insert_by_bab($bab_uuid, $berkas)
	{
		return $this->insert($bab_uuid, $berkas);
	}

    public function get_by_bab_uuid($bab_uuid)
    {
        $this->db->where('bab_uuid', $bab_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('modified_at', 'ASC');
        return $this->db->get('sub_materi')->result();
    }

	public function get_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		return $this->db->get('sub_materi')->row();
	}

	public function update($uuid, $berkas)
	{
		$data = array(
			'judul'      => $this->input->post('judul'),
			'berkas'     => $berkas,
			'modified_at'=> date("Y-m-d H:i:s")
		);
		$this->db->update('sub_materi', $data, array('uuid' => $uuid));
		return ($this->db->affected_rows() > 0);
	}

	public function delete_by_uuid($uuid)
	{
		$data = array('deleted_at' => date("Y-m-d H:i:s"));
		$this->db->update('sub_materi', $data, array('uuid' => $uuid));
		return ($this->db->affected_rows() > 0);
	}
}
?>