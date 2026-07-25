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
		$mapel_json = json_encode($namaMapel);
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
		$mapel_json = json_encode($namaMapel);
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

	public function get_all()
	{
		$this->db->select('users.*, user_profiles.mapel_uuid, user_profiles.jenis_kelamin');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where('users.role_id', 3);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->order_by('users.nama', 'ASC');
		$data = $this->db->get()->result();

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
		return $data;
	}

	public function get_mapel_pengampu($mapel_uuid, $guru_uuid)
	{
		$this->db->select('users.*, user_profiles.mapel_uuid');
		$this->db->from('users');
		$this->db->join('user_profiles', 'users.id = user_profiles.user_id', 'left');
		$this->db->where("JSON_CONTAINS(user_profiles.mapel_uuid, '\"$mapel_uuid\"')", null, false);
		$this->db->where('users.uuid', $guru_uuid);
		$this->db->where('users.role_id', 3);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$data = $this->db->get()->row();

		return $data ? true : false;
	}

}
?>