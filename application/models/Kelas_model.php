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

    public function get_all($admin_scope = TRUE)
	{
		$this->db->where('deleted_at', NULL, FALSE);
		if ($admin_scope) {
			apply_admin_creator_scope('created_by');
		}
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
	public function get_by_uuids($uuids = [], $admin_scope = TRUE)
    {
        if (empty($uuids) || !is_array($uuids)) {
            return [];
        }
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->where_in('uuid', $uuids);
		if ($admin_scope) {
			apply_admin_creator_scope('created_by');
		}
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

	public function update($uuid, $admin_scope = TRUE)
	{
		$namaKelas = $this->input->post('namaKelas');
		$data = array(
			'nama' => $namaKelas,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->where('uuid', $uuid);
		if ($admin_scope) {
			apply_admin_creator_scope('created_by');
		}
		$this->db->update('kelas', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_uuid($uuid, $admin_scope = TRUE)
	{
		$this->db->where('uuid', $uuid);
		if ($admin_scope) {
			apply_admin_creator_scope('created_by');
		}
		$data = $this->db->get('kelas')->row();
		return $data;
	}

	public function delete_by_uuid($uuid, $admin_scope = TRUE)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->where('uuid', $uuid);
		if ($admin_scope) {
			apply_admin_creator_scope('created_by');
		}
		$this->db->update('kelas', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

}