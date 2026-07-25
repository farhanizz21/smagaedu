<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends MY_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('kelas_model');
		$this->require_admin_or_superadmin();
	}

	public function index()
	{
		$data = array(
			'kelas' => $this->kelas_model->get_all(),
			'active_nav' => 'kelas'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Data Kelas']);
		$this->load->view('partials/navbar', ['active_nav' => 'kelas']);
        $this->load->view('kelas/kelas', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

    public function tambah()
	{
        $rules = $this->kelas_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->kelas_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data kelas berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data kelas gagal di simpan');
			}
			redirect('kelas');
		}

		$data = array(
			'active_nav' => 'kelas'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Kelas']);
		$this->load->view('partials/navbar', ['active_nav' => 'kelas']);
        $this->load->view('kelas/kelas-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$rules = [
			[
				'field' => 'namaKelas',
				'label' => 'Nama Kelas',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->kelas_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data kelas berhasil di Update');
				redirect('kelas');
			}else {
				$this->session->set_flashdata('error_msg', 'Data kelas gagal di Update');
				redirect('kelas');
			}
		}

		$data = array(
			'kelas' => $this->kelas_model->get_by_uuid($uuid),
			'active_nav' => 'kelas'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Kelas']);
		$this->load->view('partials/navbar', ['active_nav' => 'kelas']);
        $this->load->view('kelas/kelas-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		{
			$result = $this->kelas_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data kelas berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data kelas');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

}