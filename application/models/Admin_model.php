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

		$this->db->trans_start();
		$this->db->insert('users', $data_user);
		if ($this->db->affected_rows() > 0) {
			$this->db->insert('user_roles', array('user_id' => $this->db->insert_id(), 'role_id' => 2));
		}
		$this->db->trans_complete();
		return $this->db->trans_status() ? $uuid : false;
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
		$this->db->join('user_roles', 'user_roles.user_id = users.id');
		$this->db->join('users as creator', 'users.created_by = creator.uuid', 'left');
		$this->db->where('user_roles.role_id', 2);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->order_by('users.nama', 'ASC');
		$data = $this->db->get()->result();

		return $data;
	}

	public function get_by_uuid($uuid)
	{
		$this->db->select('users.*');
		$this->db->from('users');
		$this->db->join('user_roles', 'user_roles.user_id = users.id');
		$this->db->where('users.uuid', $uuid);
		$this->db->where('user_roles.role_id', 2);
		$this->db->where('users.deleted_at', NULL, FALSE);
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

		$admin = $this->get_by_uuid($uuid);
		if (!$admin) {
			return false;
		}

		if ($this->has_role_id($admin->id, 3)) {
			$this->db->trans_start();
			$this->db->delete('user_roles', array('user_id' => $admin->id, 'role_id' => 2));
			$role_removed = $this->db->affected_rows() > 0;
			if ($role_removed) {
				$this->db->update('users', array(
					'role_id' => 3,
					'modified_at' => date("Y-m-d H:i:s")
				), array('id' => $admin->id));
			}
			$this->db->trans_complete();
			return $role_removed && $this->db->trans_status();
		}

		$data = array('deleted_at' => date("Y-m-d H:i:s"));
		$this->db->update('users', $data, array('uuid' => $uuid));
		return ($this->db->affected_rows() > 0);
	}

	public function username_check_edit($username, $uuid)
	{
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check_edit', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}
		
		return true;
	}

	private function has_role_id($user_id, $role_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->where('role_id', $role_id);
		return $this->db->count_all_results('user_roles') > 0;
	}
}
?>