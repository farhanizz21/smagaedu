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
				'rules' => 'required|is_unique[siswa.nis]|regex_match[/^[0-9]{10}$/]',
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
				'rules' => 'required|is_unique[siswa.username]|regex_match[/^[a-z]/]'
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

	
	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('siswa', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}
}
?>