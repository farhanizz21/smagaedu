<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class Admin_model extends CI_Model {

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
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'valid_email'
			]
		];
	}

    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
        $namaLengkap = $this->input->post('namaLengkap');
        $username = $this->input->post('username');
		$email = $this->input->post('email');
        $password = 'admin12345';

		// Insert ke tabel users (role_id = 2 untuk admin)
		$data_user = array(
			'uuid' => $uuid,
			'role_id' => 2,
			'nama' => $namaLengkap,
            'username' => $username,
			'email' => $email,
            'password' =>  password_hash($password, PASSWORD_DEFAULT),
			'created_by' => $this->session->userdata('uuid'),
			'modified_at' => date("Y-m-d H:i:s")
		);

		$this->db->insert('users', $data_user);
		return ($this->db->affected_rows() > 0) ? $uuid : false;
	}

	public function update($uuid)
	{
		$namaLengkap = $this->input->post('namaLengkap');
		$username = $this->input->post('username');
		$email = $this->input->post('email');

		// Update tabel users
		$data_user = array(
			'nama' => $namaLengkap,
            'username' => $username,
			'email' => $email,
			'modified_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data_user, array('uuid' => $uuid));
		return ($this->db->affected_rows() > 0);
	}

	public function get_all()
	{
		$this->db->select('users.*, creator.nama as creator_nama');
		$this->db->from('users');
		$this->db->join('users as creator', 'users.created_by = creator.uuid', 'left');
		$this->db->where('users.role_id', 2);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->order_by('users.nama', 'ASC');
		$data = $this->db->get()->result();

		return $data;
	}

	public function get_by_uuid($uuid)
	{
		$this->db->select('users.*');
		$this->db->from('users');
		$this->db->where('users.uuid', $uuid);
		$this->db->where('users.role_id', 2);
		$data = $this->db->get()->row();
		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		// Prevent deleting self
		$current_user_uuid = $this->session->userdata('uuid');
		if ($uuid === $current_user_uuid) {
			return false;
		}

		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data, array('uuid' => $uuid, 'role_id' => 2));
		return($this->db->affected_rows() > 0) ? true : false;
	}

	public function username_check_edit($username, $uuid)
	{
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$this->db->where('role_id', 2);
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check_edit', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}
		
		return true;
	}
}
?>