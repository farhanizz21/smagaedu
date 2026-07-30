<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class mapel_model extends CI_Model {

    public function __construct()
	{
		parent::__construct();
	}

    public function rules()
	{
		return[
			[
				'field' => 'namaMapel',
				'label' => 'Nama',
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
		$data = $this->db->get('mapel')->result();

		return $data;
	}

    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
        $namaMapel = $this->input->post('namaMapel');
        $user_uuid = $this->session->userdata('uuid');

		$data = array(
			'uuid' => $uuid,
            'nama' => $namaMapel,
            'created_by' => $user_uuid
		);

		$this->db->insert('mapel', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function update($uuid)
	{
		$namaMapel = $this->input->post('namaMapel');
		$data = array(
			'nama' => $namaMapel,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('mapel', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_uuid($uuid)
	{
		$data = $this->db->get_where('mapel', array('uuid' => $uuid))->row();
		return $data;
	}
	

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('mapel', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	
	public function get_many_mapel_by_uuid($uuids = [])
	{
		if (empty($uuids)) return [];

		$this->db->where_in('uuid', $uuids);
		return $this->db->get('mapel')->result(); // kembalikan array objek mapel
	}

	/**
	 * Get all mapel that are related to a guru:
	 * - Subjects created by the guru
	 * - Subjects assigned to the guru (via mapel_uuid in user_profiles)
	 */
	public function get_all_by_guru_relation($guru_uuid, $assigned_mapel_uuids = [])
	{
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->group_start();
		$this->db->where('created_by', $guru_uuid);
		if (!empty($assigned_mapel_uuids)) {
			$this->db->or_where_in('uuid', $assigned_mapel_uuids);
		}
		$this->db->group_end();
		$this->db->order_by('modified_at', 'DESC');
		return $this->db->get('mapel')->result();
	}
}
?>