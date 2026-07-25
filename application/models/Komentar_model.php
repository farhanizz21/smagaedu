<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class Komentar_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'komentar',
				'label' => 'Komentar',
				'rules' => 'required'
			]
		];
	}

    public function insert($proyek_uuid)
	{
		$uuid = Uuid::uuid4()->toString();
        $komentar = $this->input->post('komentar');

		$data = array(
			'uuid' => $uuid,
			'proyek_uuid' => $proyek_uuid,
			'komentar' => $komentar,
			'created_by' => $this->session->userdata('uuid')
		);

		$this->db->insert('proyek_komentar', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function insert_bab_komentar($bab_uuid)
	{
		$uuid = Uuid::uuid4()->toString();
        $komentar = $this->input->post('komentar');

		$data = array(
			'uuid' => $uuid,
			'bab_uuid' => $bab_uuid,
			'komentar' => $komentar,
			'created_by' => $this->session->userdata('uuid')
		);

		$this->db->insert('bab_komentar', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function update($uuid)
	{
		$komentar = $this->input->post('komentar');
		$data = array(
			'komentar' => $komentar,
		);
		$this->db->update('proyek_komentar', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? $proyek_uuid :false;
	}

	public function get_by_proyek_uuid($proyek_uuid)
	{
		$this->db->where('proyek_uuid', $proyek_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('proyek_komentar')->result();

		return $data;
	}

	public function get_by_bab_uuid($bab_uuid)
	{
		$this->db->where('bab_uuid', $bab_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'ASC');
		$data = $this->db->get('bab_komentar')->result();

		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('proyek_komentar', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function delete_bab_komentar_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('bab_komentar', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_uuid($uuid)
	{
		$data = $this->db->get_where('proyek_komentar', array('uuid' => $uuid))->row();
		return $data;
	}

}
?>