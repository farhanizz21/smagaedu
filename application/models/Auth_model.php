<?php

class auth_model extends CI_Model
{
	private $_table_users = 'users';
	private $_table_roles = 'roles';
	private $_table_user_profiles = 'user_profiles';
	const SESSION_KEY = 'uuid';

	public function rules()
	{
		return [
			[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required'
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required'
			]
		];
	}

	/**
	 * Get role constants
	 */
	public function get_role_constants()
	{
		$roles = $this->db->get($this->_table_roles)->result();
		$role_map = [];
		foreach ($roles as $role) {
			$role_map[$role->nama] = $role->id;
		}
		return $role_map;
	}

	/**
	 * Login dengan sistem role terpusat
	 */
	public function login($username, $password)
	{
		// Join dengan tabel roles untuk mendapatkan informasi role
		$this->db->select('users.*, roles.nama as role_nama');
		$this->db->from($this->_table_users);
		$this->db->join($this->_table_roles, 'users.role_id = roles.id');
		$this->db->where('users.username', $username);
		$this->db->where('users.deleted_at', NULL);
		$this->db->where('users.status', 'aktif');
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) {
			return FALSE;
		}
		
		$user = $query->row();

		if (!password_verify($password, $user->password)) {
			return FALSE;
		}

		// Set session data
		$this->session->set_userdata([
			self::SESSION_KEY => $user->uuid, 
			'username' => $user->username, 
			'nama' => $user->nama, 
			'role' => $user->role_nama,
			'role_id' => $user->role_id
		]);
		
		return $this->session->has_userdata(self::SESSION_KEY);
	}

	/**
	 * Get current user dengan role terpusat
	 */
	public function current_user()
	{
		if (!$this->session->has_userdata(self::SESSION_KEY)) {
			return null;
		}
		
		$user_uuid = $this->session->userdata(self::SESSION_KEY);
		$role = $this->session->userdata('role');

		// Query dari tabel users dengan join roles
		$this->db->select('users.*, roles.nama as role_nama');
		$this->db->from($this->_table_users);
		$this->db->join($this->_table_roles, 'users.role_id = roles.id');
		$this->db->where('users.uuid', $user_uuid);
		
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) {
			return null;
		}

		$user = $query->row();
		
		// Tambahkan data profile jika ada
		$this->db->where('user_id', $user->id);
		$profile = $this->db->get($this->_table_user_profiles)->row();
		if ($profile) {
			$user->nip = $profile->nip;
			$user->nis = $profile->nis;
			$user->tgl_lahir = $profile->tgl_lahir;
			$user->jenis_kelamin = $profile->jenis_kelamin;
		}
		
		return $user;
	}

	/**
	 * Check if user has specific permission
	 */
	public function has_permission($permission_name, $user_uuid = null)
	{
		if ($user_uuid === null) {
			$user_uuid = $this->session->userdata(self::SESSION_KEY);
		}
		
		if (!$user_uuid) {
			return false;
		}

		// Get user role
		$this->db->select('role_id');
		$this->db->from($this->_table_users);
		$this->db->where('uuid', $user_uuid);
		$user = $this->db->get()->row();

		if (!$user) {
			return false;
		}

		// Check permission
		$this->db->from('role_permissions');
		$this->db->join('permissions', 'role_permissions.permission_id = permissions.id');
		$this->db->where('role_permissions.role_id', $user->role_id);
		$this->db->where('permissions.nama', $permission_name);
		
		return $this->db->get()->num_rows() > 0;
	}

	/**
	 * Check if user is superadmin
	 */
	public function is_superadmin($user_uuid = null)
	{
		if ($user_uuid === null) {
			$user_uuid = $this->session->userdata(self::SESSION_KEY);
		}
		
		if (!$user_uuid) {
			return false;
		}

		$this->db->select('roles.nama');
		$this->db->from($this->_table_users);
		$this->db->join($this->_table_roles, 'users.role_id = roles.id');
		$this->db->where('users.uuid', $user_uuid);
		$this->db->where('roles.nama', 'superadmin');
		
		return $this->db->get()->num_rows() > 0;
	}

	/**
	 * Logout
	 */
	public function logout()
	{
		$this->session->unset_userdata(self::SESSION_KEY);
		return !$this->session->has_userdata(self::SESSION_KEY);
	}

	/**
	 * Create new user
	 */
	public function create_user($data)
	{
		return $this->db->insert($this->_table_users, $data);
	}

	/**
	 * Update user
	 */
	public function update_user($uuid, $data)
	{
		$this->db->where('uuid', $uuid);
		return $this->db->update($this->_table_users, $data);
	}

	/**
	 * Change password for current user
	 */
	public function change_password($uuid, $new_password)
	{
		$this->db->where('uuid', $uuid);
		return $this->db->update($this->_table_users, [
			'password' => password_hash($new_password, PASSWORD_DEFAULT),
			'modified_at' => date("Y-m-d H:i:s")
		]);
	}

	/**
	 * Reset password for specific user (admin/master data only)
	 */
	public function reset_password($uuid, $new_password)
	{
		$this->db->where('uuid', $uuid);
		return $this->db->update($this->_table_users, [
			'password' => password_hash($new_password, PASSWORD_DEFAULT),
			'modified_at' => date("Y-m-d H:i:s")
		]);
	}

	/**
	 * Verify current password
	 */
	public function verify_password($uuid, $password)
	{
		$this->db->select('password');
		$this->db->where('uuid', $uuid);
		$user = $this->db->get($this->_table_users)->row();

		if ($user && password_verify($password, $user->password)) {
			return true;
		}
		return false;
	}

	/**
	 * Get user by UUID
	 */
	public function get_user_by_uuid($uuid)
	{
		$this->db->select('users.*, roles.nama as role_nama');
		$this->db->from($this->_table_users);
		$this->db->join($this->_table_roles, 'users.role_id = roles.id');
		$this->db->where('users.uuid', $uuid);
		$user = $this->db->get()->row();

		if ($user) {
			// Tambahkan data profile jika ada
			$this->db->where('user_id', $user->id);
			$profile = $this->db->get($this->_table_user_profiles)->row();
			if ($profile) {
				$user->nip = $profile->nip;
				$user->nis = $profile->nis;
				$user->tgl_lahir = $profile->tgl_lahir;
				$user->jenis_kelamin = $profile->jenis_kelamin;
			}
		}

		return $user;
	}
}