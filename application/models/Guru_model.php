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
				'rules' => 'required|callback_username_check|regex_match[/^[a-z]/]'
				
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

		// Pengaman tambahan: tolak bila username masih dipakai data aktif
		// (validasi form sudah menfilternya, ini untuk menghindari race condition)
		if ($this->is_username_dipakai_aktif($username)) {
			return false;
		}

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

	/**
	 * Cek apakah username masih dipakai oleh akun AKTIF (tabel users).
	 * Kolom users.username memiliki UNIQUE index, sehingga data yang sudah
	 * soft delete (deleted_at terisi) tidak lagi "menahan" username.
	 * Dicek untuk semua role karena unique index berlaku global.
	 */
	public function is_username_dipakai_aktif($username, $ignore_uuid = null)
	{
		$this->db->from('users');
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL);

		if ($ignore_uuid !== null && $ignore_uuid !== '') {
			$this->db->where('uuid !=', $ignore_uuid);
		}

		return $this->db->count_all_results() > 0;
	}

	/**
	 * Cek apakah NIP masih dipakai oleh profil AKTIF (user_profiles.nip).
	 * Profil milik akun yang sudah soft delete tidak lagi "menahan" NIP.
	 */
	public function is_nip_dipakai_aktif($nip, $ignore_uuid = null)
	{
		if ($nip === null || trim((string) $nip) === '') {
			return false;
		}

		$this->db->from('user_profiles');
		$this->db->join('users', 'user_profiles.user_id = users.id', 'inner');
		$this->db->where('user_profiles.nip', trim((string) $nip));
		$this->db->where('users.deleted_at', NULL);

		if ($ignore_uuid !== null && $ignore_uuid !== '') {
			$this->db->where('users.uuid !=', $ignore_uuid);
		}

		return $this->db->count_all_results() > 0;
	}

	/**
	 * Daftar username yang sudah dipakai oleh akun AKTIF (tabel users).
	 * Data yang sudah dihapus tidak lagi "menahan" username sehingga bisa
	 * dipakai ulang untuk guru baru.
	 *
	 * @return array username (lowercase) => TRUE
	 */
	public function get_existing_username()
	{
		$usernames = array();

		$this->db->select('username');
		$this->db->where('deleted_at', NULL);
		foreach ($this->db->get('users')->result() as $row) {
			if ($row->username !== NULL && $row->username !== '') {
				$usernames[strtolower($row->username)] = TRUE;
			}
		}

		return $usernames;
	}

	/**
	 * Daftar NIP yang sudah dipakai profil AKTIF (user_profiles.nip).
	 * Profil milik akun yang sudah dihapus tidak lagi menghalangi NIP sama.
	 *
	 * @return array nip => TRUE
	 */
	public function get_existing_nip()
	{
		$nip_list = array();

		$this->db->select('user_profiles.nip');
		$this->db->from('user_profiles');
		$this->db->join('users', 'user_profiles.user_id = users.id', 'inner');
		$this->db->where('users.deleted_at', NULL);

		foreach ($this->db->get()->result() as $row) {
			if ($row->nip !== NULL && trim((string) $row->nip) !== '') {
				$nip_list[trim((string) $row->nip)] = TRUE;
			}
		}

		return $nip_list;
	}

	/**
	 * Simpan satu data guru hasil import Excel.
	 * Data ditulis ke tabel users dan user_profiles dalam satu transaksi
	 * sehingga tidak ada data yang setengah jadi bila salah satu insert gagal.
	 *
	 * @param array $data nama, username, nip, mapel_uuid (JSON), jenis_kelamin (1/2)
	 * @return bool TRUE bila seluruh insert berhasil
	 */
	public function insert_import($data)
	{
		$uuid = Uuid::uuid4()->toString();
		$password = 'edu12345';
		$created_by = $this->session->userdata('uuid');
		$now = date("Y-m-d H:i:s");

		// db_debug dimatikan sementara agar kegagalan insert dikembalikan sebagai
		// false (bukan halaman error) dan pesannya bisa dilaporkan per baris.
		$db_debug = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		$this->db->trans_start();

		// Insert ke tabel users (role_id = 3 untuk guru)
		$this->db->insert('users', array(
			'uuid' => $uuid,
			'role_id' => 3,
			'nama' => $data['nama'],
			'username' => $data['username'],
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'created_by' => $created_by,
			'modified_at' => $now
		));
		$user_id = $this->db->insert_id();

		// Insert ke tabel user_profiles (nip, mapel_uuid, jenis_kelamin)
		$this->db->insert('user_profiles', array(
			'user_id' => $user_id,
			'nip' => (isset($data['nip']) && $data['nip'] !== '') ? $data['nip'] : NULL,
			'mapel_uuid' => $data['mapel_uuid'],
			'jenis_kelamin' => ($data['jenis_kelamin'] == 1) ? 'L' : 'P'
		));

		$this->db->trans_complete();

		$this->db->db_debug = $db_debug;

		return $this->db->trans_status();
	}

	/**
	 * Pesan error query terakhir, dipakai untuk laporan import Excel.
	 *
	 * @return string
	 */
	public function last_db_error()
	{
		$error = $this->db->error();

		return (isset($error['message']) && $error['message'] !== '') ? $error['message'] : 'Gagal menyimpan data ke database.';
	}

	
	/**
	 * Nonaktifkan akun login (tabel users) milik guru yang dihapus dan
	 * lepaskan username-nya (rename) agar username lama bisa dipakai ulang
	 * oleh guru baru. Username lama tetap tersimpan dengan suffix agar
	 * jejak datanya tidak hilang.
	 *
	 * @param array $uuids uuid guru (sama dengan uuid di tabel users)
	 */
	private function _lepas_akun_users($uuids)
	{
		if (!is_array($uuids) || empty($uuids)) {
			return;
		}

		$this->db->where_in('uuid', $uuids);
		$this->db->where('deleted_at', NULL);

		$this->db->set('username', "CONCAT(LEFT(username, 80), '_terhapus_', id)", FALSE);
		$this->db->set('status', 'nonaktif');
		$this->db->set('deleted_at', date("Y-m-d H:i:s"));
		$this->db->update('users');
	}

	/**
	 * Hapus beberapa data guru sekaligus (soft delete) pada tabel users.
	 * Akun login ikut dinonaktifkan dan username-nya dilepas agar bisa
	 * dipakai ulang oleh guru baru.
	 *
	 * @param array $uuids
	 * @return int jumlah baris yang terhapus
	 */
	public function delete_batch_by_uuid($uuids)
	{
		if (!is_array($uuids) || empty($uuids)) {
			return 0;
		}

		// Hitung dulu akun aktif yang akan dihapus (sebelum deleted_at diisi)
		$this->db->select('uuid');
		$this->db->where_in('uuid', $uuids);
		$this->db->where('deleted_at', NULL);
		$targets = $this->db->get('users')->result();

		if (empty($targets)) {
			return 0;
		}

		// Nonaktifkan akun, lepaskan username, dan isi deleted_at sekaligus
		$this->_lepas_akun_users(array_map(function ($row) {
			return $row->uuid;
		}, $targets));

		return count($targets);
	}

	public function delete_by_uuid($uuid)
	{
		// Pastikan akun masih aktif sebelum dihapus
		$this->db->where('uuid', $uuid);
		$this->db->where('deleted_at', NULL);
		if ($this->db->count_all_results('users') === 0) {
			return false;
		}

		// Nonaktifkan akun, lepaskan username, dan isi deleted_at sekaligus
		$this->_lepas_akun_users(array($uuid));

		return true;
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