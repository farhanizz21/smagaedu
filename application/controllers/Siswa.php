<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('siswa_model');
		$this->load->model('kelas_model');
		$this->require_admin_or_superadmin(); // Superadmin dan admin bisa akses
	}

	public function index()
	{
		$siswa = $this->siswa_model->get_all();

		$data = array(
			'siswa' => $siswa,
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Data Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('siswa/siswa', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

    public function tambah()
	{
        $rules = $this->siswa_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->siswa_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data Siswa berhasil di simpan');
				redirect('siswa');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Siswa gagal di simpan');
				redirect('siswa');
			}
		}

		$data = array(
			'daftar_kelas' => $this->kelas_model->get_all(),
			'active_nav' => 'siswa'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('siswa/siswa-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$rules = [
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],[
				'field' => 'nis',
				'label' => 'Nomor Induk Siswa',
				'rules' => 'required|regex_match[/^[0-9]{10}$/]|callback_nis_check'
			],[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[a-z]/]|callback_username_check'
			],[
				'field' => 'jenisKelamin',
				'label' => 'Jenis Kelamin',
				'rules' => 'required'
			],[
				'field' => 'tanggal_lahir',
				'label' => 'Tanggal Lahir',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->siswa_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Siswa berhasil di Update');
				redirect('siswa');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Siswa gagal di Update');
				redirect('siswa');
			}
		}

		$data = array(
			'siswa' => $this->siswa_model->get_by_uuid($uuid),
			'daftar_kelas' => $this->kelas_model->get_all(),
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('siswa/siswa-edit', $data);
		$this->load->view('partials/footer_tailwind');
	}

	public function username_check($username, $uuid)
	{
		$uuid = $this->input->post('uuid'); // atau sesuaikan dengan cara kamu ambil ID
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$query = $this->db->get('siswa');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}
		
		return true;
	}

	public function nis_check($nis, $uuid)
	{
		$uuid = $this->input->post('uuid'); // atau sesuaikan dengan cara kamu ambil ID
		$this->db->where('nis', $nis);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$query = $this->db->get('siswa');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('nis_check', 'NIS sudah digunakan oleh pengguna lain.');
			return false;
		}
		
		return true;
	}

	public function hapus($uuid){
		{
			$result = $this->siswa_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data siswa berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data siswa');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}