<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
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
		// Superadmin has all permissions, admin also needs manage_settings permission or use role check
		$this->require_role(['admin', 'superadmin']);
		
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
		$this->require_role(['admin', 'superadmin']);
		
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
}