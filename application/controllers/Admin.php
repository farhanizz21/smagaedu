<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model');
		$this->require_admin_or_superadmin(); // Admin dan superadmin bisa akses semua fitur admin
	}

	public function index()
	{
		$data = [
			'title' => 'Admin Dashboard',
			'active_nav' => 'settings'
		];
		
		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('admin/dashboard', $data);
		$this->load->view('partials/footer_tailwind');
	}

	public function settings()
	{
		$data = [
			'title' => 'Pengaturan Sistem',
			'active_nav' => 'settings'
		];
		
		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('admin/settings', $data);
		$this->load->view('partials/footer_tailwind');
	}

	public function manage_roles()
	{
		// Only superadmin can manage roles
		$this->require_superadmin();
		
		$data = [
			'title' => 'Kelola Role',
			'active_nav' => 'settings',
			'roles' => $this->db->get('roles')->result()
		];
		
		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('admin/roles', $data);
		$this->load->view('partials/footer_tailwind');
	}

	public function manage_permissions()
	{
		// Only superadmin can manage permissions
		$this->require_superadmin();
		
		$this->db->select('roles.nama as role_nama, permissions.nama as permission_nama');
		$this->db->from('role_permissions');
		$this->db->join('roles', 'role_permissions.role_id = roles.id');
		$this->db->join('permissions', 'role_permissions.permission_id = permissions.id');
		$permissions = $this->db->get()->result();

		$data = [
			'title' => 'Kelola Permission',
			'active_nav' => 'settings',
			'permissions' => $permissions
		];
		
		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('admin/permissions', $data);
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Master Data Admin - List all admin users
	 * Superadmin: full access (add, edit, delete)
	 * Admin: read only
	 */
	public function admins()
	{
		$admins = $this->admin_model->get_all();

		$data = array(
			'admins' => $admins,
			'active_nav' => 'admins'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Data Admin']);
		$this->load->view('partials/navbar', ['active_nav' => 'admins']);
        $this->load->view('admin/admins', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Tambah Admin - ONLY superadmin can add
	 */
	public function admins_tambah()
	{
		// Only superadmin can add admin users
		$this->require_superadmin();

        $rules = $this->admin_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->admin_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data admin berhasil di simpan');
				redirect('admin/admins');
			}else {
				$this->session->set_flashdata('error_msg', 'Data admin gagal di simpan');
				redirect('admin/admins');
			}
		}

		$data = array(
			'active_nav' => 'admins'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Admin']);
		$this->load->view('partials/navbar', ['active_nav' => 'admins']);
        $this->load->view('admin/admin-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Edit Admin - ONLY superadmin can edit
	 */
	public function admins_edit($uuid)
	{
		// Only superadmin can edit admin users
		$this->require_superadmin();

		$rules = [
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],
			[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[a-z]/]|callback_username_check_edit'
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'valid_email'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->admin_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Admin berhasil di Update');
				redirect('admin/admins');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Admin gagal di Update');
				redirect('admin/admins');
			}
		}

		$data = array(
			'admin' => $this->admin_model->get_by_uuid($uuid),
			'active_nav' => 'admins'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Admin']);
		$this->load->view('partials/navbar', ['active_nav' => 'admins']);
        $this->load->view('admin/admin-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Hapus Admin - ONLY superadmin can delete
	 */
	public function admins_hapus($uuid)
	{
		// Only superadmin can delete admin users
		$this->require_superadmin();

		$result = $this->admin_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data admin berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data admin');
		}
		redirect('admin/admins');
	}

	/**
	 * Username check callback for edit form
	 */
	public function username_check_edit($username)
	{
		$uuid = $this->input->post('uuid');
		return $this->admin_model->username_check_edit($username, $uuid);
	}

	// ==================== MASTER DATA KEPALA SEKOLAH ====================

	/**
	 * List all kepala_sekolah users
	 */
	public function kepala_sekolah()
	{
		$this->require_superadmin();

		$this->db->select('users.*');
		$this->db->from('users');
		$this->db->where('users.role_id', 5);
		$this->db->where('users.deleted_at', NULL, FALSE);
		$this->db->order_by('users.nama', 'ASC');
		$kepala_sekolah = $this->db->get()->result();

		$data = array(
			'kepala_sekolah' => $kepala_sekolah,
			'active_nav' => 'kepala_sekolah_admin'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Data Kepala Sekolah']);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah_admin']);
		$this->load->view('admin/kepala_sekolah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Tambah Kepala Sekolah
	 */
	public function kepala_sekolah_tambah()
	{
		$this->require_superadmin();

		$rules = [
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
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
			$namaLengkap = $this->input->post('namaLengkap');
			$username = $this->input->post('username');
			$email = $this->input->post('email');
			$password = 'admin12345';

			$data_user = array(
				'uuid' => $uuid,
				'role_id' => 5,
				'nama' => $namaLengkap,
				'username' => $username,
				'email' => $email,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'created_by' => $this->session->userdata('uuid'),
				'modified_at' => date("Y-m-d H:i:s")
			);

			$this->db->insert('users', $data_user);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('success_msg', 'Data kepala sekolah berhasil di simpan');
				redirect('admin/kepala_sekolah');
			} else {
				$this->session->set_flashdata('error_msg', 'Data kepala sekolah gagal di simpan');
				redirect('admin/kepala_sekolah');
			}
		}

		$data = array(
			'active_nav' => 'kepala_sekolah_admin'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Tambah Kepala Sekolah']);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah_admin']);
		$this->load->view('admin/kepala_sekolah-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Edit Kepala Sekolah
	 */
	public function kepala_sekolah_edit($uuid)
	{
		$this->require_superadmin();

		$rules = [
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],
			[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[a-z]/]|callback_username_check_kepsek'
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'valid_email'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$namaLengkap = $this->input->post('namaLengkap');
			$username = $this->input->post('username');
			$email = $this->input->post('email');

			$data_user = array(
				'nama' => $namaLengkap,
				'username' => $username,
				'email' => $email,
				'modified_at' => date("Y-m-d H:i:s")
			);
			$this->db->update('users', $data_user, array('uuid' => $uuid, 'role_id' => 5));
			
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('success_msg', 'Data kepala sekolah berhasil di Update');
			} else {
				$this->session->set_flashdata('error_msg', 'Data kepala sekolah gagal di Update');
			}
			redirect('admin/kepala_sekolah');
		}

		$kepsek = $this->db->get_where('users', array('uuid' => $uuid, 'role_id' => 5))->row();
		if (!$kepsek) {
			show_404();
		}

		$data = array(
			'kepsek' => $kepsek,
			'active_nav' => 'kepala_sekolah_admin'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Kepala Sekolah']);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah_admin']);
		$this->load->view('admin/kepala_sekolah-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Username check callback for kepala_sekolah edit
	 */
	public function username_check_kepsek($username)
	{
		$uuid = $this->input->post('uuid');
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$this->db->where('role_id', 5);
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check_kepsek', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}
		return true;
	}

	/**
	 * Hapus Kepala Sekolah
	 */
	public function kepala_sekolah_hapus($uuid)
	{
		$this->require_superadmin();

		$current_user_uuid = $this->session->userdata('uuid');
		if ($uuid === $current_user_uuid) {
			$this->session->set_flashdata('error_msg', 'Tidak dapat menghapus akun sendiri');
			redirect('admin/kepala_sekolah');
		}

		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('users', $data, array('uuid' => $uuid, 'role_id' => 5));
		
		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('success_msg', 'Data kepala sekolah berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data kepala sekolah');
		}
		redirect('admin/kepala_sekolah');
	}
}