<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller with Role Checking
 * Extend semua controller dari MY_Controller untuk fitur role checking
 */
class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->library('form_validation');
		
		// Cek login
		if (!$this->auth_model->current_user()) {
			redirect('login');
		}
	}

	/**
	 * Middleware: Require specific role(s)
	 * Superadmin automatically has access to all roles
	 * @param array|string $roles Role name(s) required
	 */
	protected function require_role($roles)
	{
		if (!require_role_access($roles)) {
			show_error('Anda tidak memiliki akses ke halaman ini. Role yang diperlukan: ' . (is_array($roles) ? implode(', ', $roles) : $roles), 403);
		}
	}

	/**
	 * Middleware: Require specific permission
	 * Superadmin automatically has all permissions
	 * @param string $permission Permission name required
	 */
	protected function require_permission($permission)
	{
		if (!has_permission($permission)) {
			show_error('Anda tidak memiliki izin untuk mengakses halaman ini. Permission yang diperlukan: ' . $permission, 403);
		}
	}

	/**
	 * Middleware: Require superadmin
	 */
	protected function require_superadmin()
	{
		if (!is_superadmin()) {
			show_error('Halaman ini hanya bisa diakses oleh Superadmin.', 403);
		}
	}

	/**
	 * Middleware: Require admin or superadmin
	 * Superadmin has access to all admin features
	 */
	protected function require_admin_or_superadmin()
	{
		if (!has_role(['admin', 'superadmin'])) {
			show_error('Halaman ini hanya bisa diakses oleh Admin atau Superadmin.', 403);
		}
	}

	/**
	 * Middleware: Require kepala_sekolah or superadmin
	 */
	protected function require_kepala_sekolah_or_superadmin()
	{
		if (!has_role(['kepala_sekolah', 'superadmin'])) {
			show_error('Halaman ini hanya bisa diakses oleh Kepala Sekolah atau Superadmin.', 403);
		}
	}
}