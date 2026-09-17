<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class kelas_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'namaKelas',
				'label' => 'Nama Kelas',
				'rules' => 'required'
			]
		];
	}

    public function get_all()
	{
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('kelas')->result();

		return $data;
	}

    /**
     * Get kelas by array of UUIDs.
     *
     * @param array $uuids Daftar UUID kelas
     * @return array Daftar kelas
     */
    public function get_by_uuids($uuids = [])
    {
        if (empty($uuids) || !is_array($uuids)) {
            return [];
        }
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->where_in('uuid', $uuids);
        $this->db->order_by('modified_at', 'DESC');
        return $this->db->get('kelas')->result();
    }

    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
        $namaKelas = $this->input->post('namaKelas');

		$data = array(
			'uuid' => $uuid,
			'nama' => $namaKelas,
			'created_by' => $this->session->userdata('uuid')
		);

		$this->db->insert('kelas', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function update($uuid)
	{
		$namaKelas = $this->input->post('namaKelas');
		$data = array(
			'nama' => $namaKelas,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('kelas', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_uuid($uuid)
	{
		$data = $this->db->get_where('kelas', array('uuid' => $uuid))->row();
		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('kelas', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

}