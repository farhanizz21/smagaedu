<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class siswa_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'nis',
				'label' => 'Nomor Induk Siswa',
				'rules' => 'required|callback_nis_check|regex_match[/^[0-9]{10}$/]',
				'errors' => array(
				'regex_match' => 'Kolom {field} hanya menerima angka dengan 10 angka'
			),
			],
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
				'field' => 'tanggal_lahir',
				'label' => 'Tanggal Lahir',
				'rules' => 'required'
			],
			[
				'field' => 'jenisKelamin',
				'label' => 'Jenis Kelamin',
				'rules' => 'required'
			],
			[
				'field' => 'kelas',
				'label' => 'Kelas',
				'rules' => 'required'
			]
            ];
	}

    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
		$nis = $this->input->post('nis');
        $namaLengkap = $this->input->post('namaLengkap');
		$username = $this->input->post('username');
        $password = 'edu12345';
		$tanggal_lahir = $this->input->post('tanggal_lahir');
		$jenisKelamin = $this->input->post('jenisKelamin');
		$kelas = $this->input->post('kelas');

		// Pengaman tambahan: tolak bila NIS/username masih dipakai data aktif
		// (validasi form sudah menfilternya, ini untuk menghindari race condition)
		if ($this->is_nis_dipakai_aktif($nis) || $this->is_username_dipakai_aktif($username)) {
			return false;
		}

		// Insert ke tabel users (role_id = 4 untuk siswa)
		$data_user = array(
			'uuid' => $uuid,
			'role_id' => 4,
			'nama' => $namaLengkap,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
			'created_by' => $this->session->userdata('uuid'),
			'modified_at' => date("Y-m-d H:i:s")
		);

		$this->db->insert('users', $data_user);
		if ($this->db->affected_rows() > 0) {
			$user_id = $this->db->insert_id();

			// Insert ke tabel user_profiles (nis, tgl_lahir, jenis_kelamin)
			$jenis_kelamin_map = [
				1 => 'L',
				2 => 'P'
			];

			$data_profile = array(
				'user_id' => $user_id,
				'nis' => $nis,
				'tgl_lahir' => $tanggal_lahir,
				'jenis_kelamin' => isset($jenis_kelamin_map[$jenisKelamin]) ? $jenis_kelamin_map[$jenisKelamin] : null
			);

			$this->db->insert('user_profiles', $data_profile);

			// Insert ke tabel siswa (legacy)
			$data_siswa = array(
				'uuid' => $uuid,
				'nis' => $nis,
				'nama' => $namaLengkap,
				'username' => $username,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'tgl_lahir' => $tanggal_lahir,
				'jenis_kelamin' => $jenisKelamin,
				'kelas_uuid' => $kelas
			);
			$this->db->insert('siswa', $data_siswa);

			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}

		return false;
	}

	public function update($uuid)
	{
		$nis = $this->input->post('nis');
		$namaLengkap = $this->input->post('namaLengkap');
		$username = $this->input->post('password');
		$tanggal_lahir = $this->input->post('tanggal_lahir');
		$jenisKelamin = $this->input->post('jenisKelamin');
		$kelas = $this->input->post('kelas');
		$username = $this->input->post('username');

		// Update tabel users
		$data_user = array(
			'nama' => $namaLengkap,
			'username' => $username,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data_user, array('uuid' => $uuid));

		// Update tabel siswa (legacy)
		$data_siswa = array(
			'nis' => $nis,
			'nama' => $namaLengkap,
			'username' => $username,
			'tgl_lahir' => $tanggal_lahir,
			'jenis_kelamin' => $jenisKelamin,
			'kelas_uuid' => $kelas
		);
		$this->db->update('siswa', $data_siswa, array('uuid' => $uuid));

		// Update tabel user_profiles
		$user = $this->db->get_where('users', array('uuid' => $uuid))->row();
		if ($user) {
			$jenis_kelamin_map = [
				1 => 'L',
				2 => 'P'
			];

			$data_profile = array(
				'nis' => $nis,
				'tgl_lahir' => $tanggal_lahir,
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

	public function get_by_uuid($uuid)
	{
		$this->db->select("siswa.*, kelas.nama as kelas_nama");
		$this->db->join('kelas', 'siswa.kelas_uuid = kelas.uuid', 'left');
		$data = $this->db->get_where('siswa', array('siswa.uuid' => $uuid))->row();
		return $data;
	}
	

	public function insert_on_ujian()
	{
		$uuid = Uuid::uuid4()->toString();
		$ujian_uuid = $this->input->post('ujian_uuid');
        $siswa_uuid = $this->input->post('siswa');
		$data = array(
			'uuid' => $uuid,
			'ujian_uuid' => $ujian_uuid,
			'siswa_uuid' => $siswa_uuid,
			'created_by' => $this->session->userdata('uuid')
		);
		$this->db->insert('ujian_siswa', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function get_all()
	{
		$this->db->select("siswa.*, DATE_FORMAT(siswa.tgl_lahir, '%d-%m-%Y') as tgl_lahir_formatted, kelas.nama as kelas_nama", FALSE);
		$this->db->join('kelas', 'siswa.kelas_uuid = kelas.uuid', 'left');
		$this->db->where('siswa.deleted_at IS NULL', NULL, FALSE);
		$this->db->order_by('siswa.id', 'DESC');
		$data = $this->db->get('siswa')->result();

		foreach ($data as $key) {
			$key->jenis_kelamin = ($key->jenis_kelamin == 1) ? 'Laki-laki' : 'Perempuan';
		}

		return $data;
	}

		/**
	 * Daftar username yang sudah dipakai oleh data AKTIF (tabel users & siswa).
	 * Data yang sudah soft delete (deleted_at terisi) tidak lagi "menahan"
	 * username, sehingga NIS/username lama bisa dipakai ulang setelah hapus.
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

		$this->db->select('username');
		$this->db->where('deleted_at', NULL);
		foreach ($this->db->get('siswa')->result() as $row) {
			if ($row->username !== NULL && $row->username !== '') {
				$usernames[strtolower($row->username)] = TRUE;
			}
		}

		return $usernames;
	}

	/**
	 * Daftar NIS yang sudah terdaftar pada data AKTIF (tabel siswa &
	 * user_profiles). Data yang sudah soft delete tidak lagi "menahan" NIS.
	 *
	 * @return array nis => TRUE
	 */
	public function get_existing_nis()
	{
		$nis_list = array();

		$this->db->select('nis');
		$this->db->where('deleted_at', NULL);
		foreach ($this->db->get('siswa')->result() as $row) {
			if ($row->nis !== NULL && trim((string) $row->nis) !== '') {
				$nis_list[(int) $row->nis] = TRUE;
			}
		}

		// user_profiles tidak punya kolom deleted_at; hanya ambil NIS milik
		// akun users yang masih aktif (belum soft delete).
		$this->db->select('user_profiles.nis');
		$this->db->from('user_profiles');
		$this->db->join('users', 'user_profiles.user_id = users.id', 'inner');
		$this->db->where('users.deleted_at', NULL);
		foreach ($this->db->get()->result() as $row) {
			if ($row->nis !== NULL && trim((string) $row->nis) !== '') {
				$nis_list[(int) $row->nis] = TRUE;
			}
		}

		return $nis_list;
	}

	/**
	 * Simpan satu data siswa hasil import Excel.
	 * Data ditulis ke tabel users, user_profiles, dan siswa dalam satu transaksi
	 * sehingga tidak ada data yang setengah jadi bila salah satu insert gagal.
	 *
	 * @param array $data nis, nama, username, tgl_lahir, jenis_kelamin (1/2), kelas_uuid
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

		// Insert ke tabel users (role_id = 4 untuk siswa)
		$this->db->insert('users', array(
			'uuid' => $uuid,
			'role_id' => 4,
			'nama' => $data['nama'],
			'username' => $data['username'],
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'created_by' => $created_by,
			'modified_at' => $now
		));
		$user_id = $this->db->insert_id();

		// Insert ke tabel user_profiles (nis, tgl_lahir, jenis_kelamin)
		$this->db->insert('user_profiles', array(
			'user_id' => $user_id,
			'nis' => $data['nis'],
			'tgl_lahir' => $data['tgl_lahir'],
			'jenis_kelamin' => ($data['jenis_kelamin'] == 1) ? 'L' : 'P'
		));

		// Insert ke tabel siswa (legacy)
		$this->db->insert('siswa', array(
			'uuid' => $uuid,
			'nis' => $data['nis'],
			'nama' => $data['nama'],
			'username' => $data['username'],
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'tgl_lahir' => $data['tgl_lahir'],
			'jenis_kelamin' => $data['jenis_kelamin'],
			'kelas_uuid' => $data['kelas_uuid'],
			'created_by' => $created_by,
			'modified_at' => $now
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

	public function get_by_kelompok_uuid($kelompok_uuid)
	{
		$this->db->select("siswa_uuid");
		$this->db->where('kelompok_uuid', $kelompok_uuid);
		$data = $this->db->get('kelompok_siswa');

		return $data->result();
	}

    public function insert_by_kelas($ujian_uuid, $kelas_uuid)
    {
        $this->db->select('s.uuid');
        $this->db->from('siswa s');
        $this->db->where('s.kelas_uuid', $kelas_uuid);
        $this->db->where('s.deleted_at', NULL, FALSE);
        $this->db->join('ujian_siswa u', "u.siswa_uuid = s.uuid AND u.ujian_uuid = '{$ujian_uuid}' AND u.deleted_at IS NULL", 'left');
        $this->db->where('u.uuid', NULL, FALSE);
        $siswa_belum_diambil = $this->db->get()->result();

        $count = 0;
        foreach($siswa_belum_diambil as $siswa) {
            $uuid = Uuid::uuid4()->toString();
            $data = array(
                'uuid' => $uuid,
                'ujian_uuid' => $ujian_uuid,
                'siswa_uuid' => $siswa->uuid,
                'created_by' => $this->session->userdata('uuid')
            );
            $this->db->insert('ujian_siswa', $data);
            if ($this->db->affected_rows() > 0) {
                $count++;
            }
        }

        return $count > 0;
    }

    public function get_by_ujian($ujian_uuid)
	{
		$this->db->select("s.nama, s.username, s.uuid AS siswa_uuid, u.uuid, u.ujian_uuid, u.siswa_uuid, u.ujian_nilai, u.modified_at, k.nama as kelas_nama");
		$this->db->join('siswa s', 's.uuid = u.siswa_uuid','left');
		$this->db->join('kelas k', 's.kelas_uuid = k.uuid','left');
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->where('u.ujian_uuid', $ujian_uuid);
		$data = $this->db->get('ujian_siswa u');

		return $data->result();
	}

    public function get_by_proyek($proyek_uuid)
	{
		$this->db->select("k.*, s.nama, ");
		$this->db->join('kelompok k', 'ks.kelompok_uuid = k.uuid','left');
		$this->db->join('siswa s', 's.uuid = ks.siswa_uuid','left');
		$this->db->where('ks.deleted_at', NULL, FALSE);
		$this->db->where('ks.proyek_uuid', $proyek_uuid);
		$data = $this->db->get('kelompok_siswa ks');

		return $data->result();
	}

	// public function delete_siswa_ujian_by_uuid($relasi_uuid)
	// {
	// 	$data = array(
	// 		'deleted_at' => date("Y-m-d H:i:s")
	// 	);
	// 	$this->db->update('ujian_siswa', $data, array('uuid' => $relasi_uuid));
	// 	return($this->db->affected_rows() > 0) ? true :false;
	// }

	public function delete_siswa_ujian_and_ujian_jawaban_by_uuid($relasi_uuid)
	{
		// Step 1: Ambil data ujian_uuid dan siswa_uuid
		$this->db->where('uuid', $relasi_uuid);
		$this->db->where('deleted_at', NULL);
		$row = $this->db->get('ujian_siswa')->row();

		if (!$row) return false;

		$ujian_uuid = $row->ujian_uuid;
		$siswa_uuid = $row->siswa_uuid;
		$now = date("Y-m-d H:i:s");

		// Step 2: Soft delete ujian_siswa
		$this->db->where('uuid', $relasi_uuid);
		$this->db->update('ujian_siswa', ['deleted_at' => $now]);

		// Step 3: Soft delete ujian_jawaban yang sesuai
		$this->db->where('ujian_uuid', $ujian_uuid);
		$this->db->where('created_by', $siswa_uuid);
		$this->db->update('ujian_jawaban', ['deleted_at' => $now]);

		return true;
	}

	
			/**
	 * Cek apakah NIS masih dipakai oleh data siswa yang AKTIF (belum dihapus).
	 * Data yang sudah soft delete (deleted_at terisi) tidak lagi "menahan" NIS.
	 */
	public function is_nis_dipakai_aktif($nis, $ignore_uuid = null)
	{
		$this->db->from('siswa');
		$this->db->where('nis', $nis);
		$this->db->where('deleted_at', NULL);

		if ($ignore_uuid !== null && $ignore_uuid !== '') {
			$this->db->where('uuid !=', $ignore_uuid);
		}

		return $this->db->count_all_results() > 0;
	}

	/**
	 * Cek apakah username masih dipakai oleh data AKTIF.
	 * Dicek di dua tabel: siswa (data aktif) dan users (data aktif, karena
	 * users.username memiliki UNIQUE index di database).
	 * Data yang sudah soft delete tidak lagi "menahan" username.
	 */
	public function is_username_dipakai_aktif($username, $ignore_uuid = null)
	{
		$this->db->from('siswa');
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL);

		if ($ignore_uuid !== null && $ignore_uuid !== '') {
			$this->db->where('uuid !=', $ignore_uuid);
		}

		if ($this->db->count_all_results() > 0) {
			return true;
		}

		$this->db->from('users');
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL);

		if ($ignore_uuid !== null && $ignore_uuid !== '') {
			$this->db->where('uuid !=', $ignore_uuid);
		}

		return $this->db->count_all_results() > 0;
	}

	/**
	 * Nonaktifkan akun login (tabel users) milik siswa yang dihapus dan
	 * lepaskan username-nya (rename) agar username lama bisa dipakai ulang
	 * oleh siswa baru. Username lama tetap tersimpan dengan suffix agar
	 * jejak datanya tidak hilang.
	 *
	 * @param array $uuids uuid siswa (sama dengan uuid di tabel users)
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

	public function delete_batch_by_uuid($uuids)
	{
		if (!is_array($uuids) || empty($uuids)) {
			return 0;
		}

		// Soft delete pada tabel siswa (konsisten dengan delete_by_uuid).
		// Perlu diketahui: hanya baris tabel siswa yang dihapus; akun login
		// di tabel users & user_profiles tetap ada. Jika ingin menghapus pula
		// akun loginnya, lakukan soft delete juga pada tabel users.
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);

		$this->db->where_in('uuid', $uuids);
		$this->db->update('siswa', $data);

		$deleted = $this->db->affected_rows();

		// Nonaktifkan akun login & lepaskan username agar bisa dipakai ulang
		$this->_lepas_akun_users($uuids);

		return $deleted;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('siswa', $data, array('uuid' => $uuid));
		$result = ($this->db->affected_rows() > 0) ? true : false;

		// Nonaktifkan akun login & lepaskan username agar bisa dipakai ulang
		if ($result) {
			$this->_lepas_akun_users(array($uuid));
		}

		return $result;
	}
}
?>