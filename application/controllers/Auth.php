<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('auth_model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		
	}

	public function login()
	{
		if($this->auth_model->current_user()){
			redirect(base_url());
		}
		
		$rules = $this->auth_model->rules();
		$this->form_validation->set_rules($rules);

		if($this->form_validation->run() == FALSE){
			return $this->load->view('auth/login');
		}

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		if($this->auth_model->login($username, $password)){
			redirect(base_url());
		} else {
			$this->session->set_flashdata('error_msg', 'Login Gagal, pastikan username dan password benar!');	
        }
        $this->load->view('auth/login');
    }


	public function logout()
	{
		$this->auth_model->logout();
		redirect(base_url());
	}

	// Ganti password untuk semua user yang sudah login
	public function ganti_password()
	{
		if (!$this->auth_model->current_user()) {
			redirect('auth/login');
		}

		$rules = [
			[
				'field' => 'current_password',
				'label' => 'Password Lama',
				'rules' => 'required'
			],
			[
				'field' => 'new_password',
				'label' => 'Password Baru',
				'rules' => 'required|min_length[6]'
			],
			[
				'field' => 'confirm_password',
				'label' => 'Konfirmasi Password',
				'rules' => 'required|matches[new_password]'
			]
		];

		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$user = $this->auth_model->current_user();
			$current_password = $this->input->post('current_password');
			$new_password = $this->input->post('new_password');

			// Verifikasi password lama
			if (!$this->auth_model->verify_password($user->uuid, $current_password)) {
				$this->session->set_flashdata('error_msg', 'Password lama tidak sesuai!');
				redirect('auth/ganti_password');
			}

			// Update password
			if ($this->auth_model->change_password($user->uuid, $new_password)) {
				$this->session->set_flashdata('success_msg', 'Password berhasil diubah!');
				redirect('auth/ganti_password');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal mengubah password!');
				redirect('auth/ganti_password');
			}
		}

		$data = [
			'title' => 'Ganti Password',
			'active_nav' => '',
			'from_controller' => true
		];

		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('auth/ganti_password', $data);
		$this->load->view('partials/footer_tailwind');
	}

	// Reset password untuk admin (master data guru dan siswa)
	public function reset_password($uuid)
	{
		// Cek apakah admin login
		if (!$this->auth_model->current_user()) {
			redirect('auth/login');
		}

		// Hanya admin dan superadmin yang bisa reset password
		$user = $this->auth_model->current_user();
		if (!in_array($user->role_nama, ['admin', 'superadmin'])) {
			$this->session->set_flashdata('error_msg', 'Akses ditolak!');
			redirect(base_url());
		}

		$rules = [
			[
				'field' => 'new_password',
				'label' => 'Password Baru',
				'rules' => 'required|min_length[6]'
			],
			[
				'field' => 'confirm_password',
				'label' => 'Konfirmasi Password',
				'rules' => 'required|matches[new_password]'
			]
		];

		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$new_password = $this->input->post('new_password');

			if ($this->auth_model->reset_password($uuid, $new_password)) {
				$this->session->set_flashdata('success_msg', 'Password berhasil direset!');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal mereset password!');
			}

			// Redirect kembali ke halaman sebelumnya
			redirect($_SERVER['HTTP_REFERER']);
		}

		// Jika GET request atau validasi gagal, tampilkan form
		$target_user = $this->auth_model->get_user_by_uuid($uuid);
		
		if (!$target_user) {
			$this->session->set_flashdata('error_msg', 'User tidak ditemukan!');
			redirect($_SERVER['HTTP_REFERER']);
		}

		$data = [
			'title' => 'Reset Password',
			'active_nav' => '',
			'target_user' => $target_user,
			'from_controller' => true
		];

		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('auth/reset_password', $data);
		$this->load->view('partials/footer_tailwind');
	}
}