<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class guru_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],
			[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|is_unique[users.username]|regex_match[/^[a-z]/]'
				
			],
			[
				'field' => 'namaMapel[]',
				'label' => 'Mata Pelajaran',
				'rules' => 'required'
			],
			[
				'field' => 'jenisKelamin',
				'label' => 'Jenis Kelamin',
				'rules' => 'required'
			],
		];
	}

    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
        $namaLengkap = $this->input->post('namaLengkap');
        $username = $this->input->post('username');
        $password = 'edu12345';
		$namaMapel = $this->input->post('namaMapel'); // array dari select multiple
		$kelasMapel = $this->input->post('kelasMapel'); // associative array [mapel_uuid => [kelas_uuid, ...]]

		// Build new JSON format: array of {mapel_uuid, kelas_list}
		$mapel_kelas_data = [];
		if (!empty($namaMapel)) {
			foreach ($namaMapel as $mapel_uuid) {
				$entry = [
					'mapel_uuid' => $mapel_uuid,
					'kelas_list' => isset($kelasMapel[$mapel_uuid]) ? $kelasMapel[$mapel_uuid] : []
				];
				$mapel_kelas_data[] = $entry;
			}
		}
		$mapel_json = json_encode($mapel_kelas_data);
        $jenisKelamin = $this->input->post('jenisKelamin');

		// Insert ke tabel users (role_id = 3 untuk guru)
		$data_user = array(
			'uuid' => $uuid,
			'role_id' => 3,
			'nama' => $namaLengkap,
            'username' => $username,
            'password' =>  password_hash($password, PASSWORD_DEFAULT),
			'created_by' => $this->session->userdata('uuid'),
			'modified_at' => date("Y-m-d H:i:s")
		);

		$this->db->insert('users', $data_user);
		if ($this->db->affected_rows() > 0) {
			// Dapatkan ID user yang baru saja diinsert
			$user_id = $this->db->insert_id();

			// Insert ke tabel user_profiles
			$jenis_kelamin_map = [
				1 => 'L',
				2 => 'P'
			];

			$data_profile = array(
				'user_id' => $user_id,
				'mapel_uuid' => $mapel_json,
				'jenis_kelamin' => isset($jenis_kelamin_map[$jenisKelamin]) ? $jenis_kelamin_map[$jenisKelamin] : null
			);

			$this->db->insert('user_profiles', $data_profile);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
		
		return false;
	}

	public function update($uuid)
	{
		$namaLengkap = $this->input->post('namaLengkap');
		$username = $this->input->post('username');
		$namaMapel = $this->input->post('namaMapel'); // array dari select multiple
		$kelasMapel = $this->input->post('kelasMapel'); // associative array [mapel_uuid => [kelas_uuid, ...]]

		// Build new JSON format: array of {mapel_uuid, kelas_list}
		$mapel_kelas_data = [];
		if (!empty($namaMapel)) {
			foreach ($namaMapel as $mapel_uuid) {
				$entry = [
					'mapel_uuid' => $mapel_uuid,
					'kelas_list' => isset($kelasMapel[$mapel_uuid]) ? $kelasMapel[$mapel_uuid] : []
				];
				$mapel_kelas_data[] = $entry;
			}
		}
		$mapel_json = json_encode($mapel_kelas_data);
		$jenisKelamin = $this->input->post('jenisKelamin');

		// Update tabel users
		$data_user = array(
			'nama' => $namaLengkap,
            'username' => $username,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data_user, array('uuid' => $uuid));

		// Update tabel user_profiles
		// Dapatkan user_id dari uuid
		$user = $this->db->get_where('users', array('uuid' => $uuid))->row();
		if ($user) {
			$jenis_kelamin_map = [
				1 => 'L',
				2 => 'P'
			];

			$data_profile = array(
				'mapel_uuid' => $mapel_json,
				'jenis_kelamin' => isset($jenis_kelamin_map[$jenisKelamin]) ? $jenis_kelamin_map[$jenisKelamin] : null
			);

			// Cek apakah profile sudah ada
			$profile = $this->db->get_where('user_profiles', array('user_id' => $user->id))->row();
			if ($profile) {
				$this->db->update('user_profiles', $data_profile, array('user_id' => $user->id));
			} else {
				$data_profile['user_id'] = $user->id;
				$this->db->insert('user_profiles', $data_profile);
			}
		}

		return true;
	}

	/**
	 * Parse the mapel_uuid field - handles both old format (simple array of UUIDs)
	 * and new format (array of {mapel_uuid, kelas_list} objects)
	 */
	private function parse_mapel_data($mapel_uuid_json)
	{
		if (empty($mapel_uuid_json)) {
			return ['mapel_list' => [], 'kelas_map' => []];
		}

		$data = json_decode($mapel_uuid_json, true);
		if (!is_array($data)) {
			return ['mapel_list' => [], 'kelas_map' => []];
		}

		// Check if old format (simple array of mapel UUIDs) or new format
		if (isset($data[0]) && is_string($data[0])) {
			// Old format: ["uuid1", "uuid2"]
			return [
				'mapel_list' => $data,
				'kelas_map' => []
			];
		}

		// New format: [{"mapel_uuid": "uuid1", "kelas_list": ["kelas1", "kelas2"]}]
		$mapel_list = [];
		$kelas_map = [];
		foreach ($data as $item) {
			if (isset($item['mapel_uuid'])) {
				$mapel_list[] = $item['mapel_uuid'];
				$kelas_map[$item['mapel_uuid']] = isset($item['kelas_list']) ? $item['kelas_list'] : [];
			}
		}

		return [
			'mapel_list' => $mapel_list,
			'kelas_map' => $kelas_map
		];
	}

	public function get_all()
	{
		$this->db->select('users.*, user_profiles.mapel_uuid, user_profiles.jenis_kelamin');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.role_id', 3);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->order_by('users.nama', 'ASC');
		$data = $this->db->get()->result();

		// Enrich data with mapel and kelas names
		foreach ($data as $val) {
			$parsed = $this->parse_mapel_data($val->mapel_uuid);
			
			// Get mapel names
			$mapel_list = $this->get_mapel_names($parsed['mapel_list']);
			$val->mapel_nama = array_map(function($m) {
				return $m->nama;
			}, $mapel_list);
			$val->mapel_data = $mapel_list; // full mapel objects with uuid

			// Get kelas names for each mapel
			$val->kelas_per_mapel = [];
			foreach ($parsed['kelas_map'] as $mapel_uuid => $kelas_uuids) {
				$kelas_names = $this->get_kelas_names($kelas_uuids);
				$val->kelas_per_mapel[$mapel_uuid] = $kelas_names;
			}
		}

		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}
	
	public function get_by_uuid($uuid)
	{
		$this->db->select('users.*, user_profiles.mapel_uuid, user_profiles.jenis_kelamin');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.uuid', $uuid);
		$this->db->where('users.role_id', 3);
		$data = $this->db->get()->row();

		if ($data) {
			$parsed = $this->parse_mapel_data($data->mapel_uuid);
			$data->mapel_list = $parsed['mapel_list'];
			$data->kelas_map = $parsed['kelas_map'];
		}

		return $data;
	}

	/**
	 * Get mapel objects by array of UUIDs
	 */
	private function get_mapel_names($uuids = [])
	{
		if (empty($uuids)) return [];
		$this->db->where_in('uuid', $uuids);
		return $this->db->get('mapel')->result();
	}

	/**
	 * Get kelas names by array of UUIDs
	 */
	private function get_kelas_names($uuids = [])
	{
		if (empty($uuids)) return [];
		$this->db->where_in('uuid', $uuids);
		return $this->db->get('kelas')->result();
	}

	public function get_mapel_pengampu($mapel_uuid, $guru_uuid)
	{
		$this->db->select('users.*, user_profiles.mapel_uuid');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.uuid', $guru_uuid);
		$this->db->where('users.role_id', 3);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$data = $this->db->get()->row();

		if (!$data || empty($data->mapel_uuid)) {
			return false;
		}

		// Parse the mapel data (handles both old and new format)
		$parsed = $this->parse_mapel_data($data->mapel_uuid);
		
		// Check if the mapel_uuid exists in the parsed mapel list
		return in_array($mapel_uuid, $parsed['mapel_list']);
	}

	/**
	 * Get mapel UUID list from user_profiles.mapel_uuid - handles both old and new format
	 */
	public function get_mapel_uuid_list($guru_uuid)
	{
		$this->db->select('user_profiles.mapel_uuid');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.uuid', $guru_uuid);
		$data = $this->db->get()->row();

		if (!$data || empty($data->mapel_uuid)) {
			return [];
		}

		$parsed = $this->parse_mapel_data($data->mapel_uuid);
		return $parsed['mapel_list'];
	}

}
?>