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

	/**
	 * Get array of mapel UUIDs yang diampu untuk kelas tertentu.
	 * Mapel-kelas relationship disimpan pada user_profiles.mapel_uuid
	 * dalam format JSON: [{"mapel_uuid": "uuid1", "kelas_list": ["kelas1", "kelas2"]}]
	 *
	 * @param string $kelas_uuid UUID kelas siswa
	 * @return array Daftar UUID mapel yang diampu untuk kelas tersebut
	 */
	public function get_mapel_uuids_by_kelas($kelas_uuid)
	{
		if (empty($kelas_uuid)) {
			return [];
		}

		// Ambil semua data mapel_uuid dari user_profiles (guru)
		$this->db->select('user_profiles.mapel_uuid');
		$this->db->from('user_profiles');
		$this->db->join('users', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.role_id', 3); // guru
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->where('user_profiles.mapel_uuid IS NOT NULL', NULL, FALSE);
		$profiles = $this->db->get()->result();

		// Kumpulkan mapel_uuid yang diampu untuk kelas ini
		$mapel_uuids = [];
		foreach ($profiles as $profile) {
			$data = json_decode($profile->mapel_uuid, true);
			if (!is_array($data)) {
				continue;
			}

			// Handle format lama (array of string UUID)
			if (isset($data[0]) && is_string($data[0])) {
				foreach ($data as $uuid) {
					$mapel_uuids[$uuid] = true;
				}
				continue;
			}

			// Handle format baru (array of {mapel_uuid, kelas_list})
			foreach ($data as $item) {
				if (!isset($item['mapel_uuid'])) {
					continue;
				}
				$kelas_list = isset($item['kelas_list']) ? $item['kelas_list'] : [];
				// Jika tidak ada kelas_list (kosong), anggap diampu untuk semua kelas
				if (empty($kelas_list) || in_array($kelas_uuid, $kelas_list)) {
					$mapel_uuids[$item['mapel_uuid']] = true;
				}
			}
		}

		return array_keys($mapel_uuids);
	}

	/**
	 * Get all mapel yang diampu untuk kelas tertentu.
	 *
	 * @param string $kelas_uuid UUID kelas siswa
	 * @return array Daftar mapel yang diampu untuk kelas tersebut
	 */
	public function get_all_by_kelas($kelas_uuid)
	{
		$mapel_uuids = $this->get_mapel_uuids_by_kelas($kelas_uuid);

		if (empty($mapel_uuids)) {
			return [];
		}

		// Ambil data mapel yang belum dihapus
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('uuid', $mapel_uuids);
		$this->db->order_by('modified_at', 'DESC');
		return $this->db->get('mapel')->result();
	}
}
?>