<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel extends MY_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('mapel_model');
		$this->require_admin_or_superadmin(); // Superadmin dan admin bisa akses
	}

	public function index()
	{
		$data = array(
			'mapel' => $this->mapel_model->get_all(),
			'active_nav' => 'mapel'
		);
		
        $this->load->view('partials/header_tailwind', ['title' => 'Data Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('mapel/mapel', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
    
    public function tambah()
	{
        $rules = $this->mapel_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->mapel_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data mata pelajaran berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data mata pelajaran gagal di simpan');
			}
			redirect('mapel');
		}

		$data = array(
			'active_nav' => 'mapel'
		);
        
        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('mapel/mapel-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$rules = [
			[
				'field' => 'namaMapel',
				'label' => 'Nama Mata Pelajaran',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->mapel_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Mata Pelajaran berhasil di Update');
				redirect('mapel');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Mata Pelajaran gagal di Update');
				redirect('mapel');
			}
		}

		$data = array(
			'mapel' => $this->mapel_model->get_by_uuid($uuid),
			'active_nav' => 'mapel'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('mapel/mapel-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		{
			$result = $this->mapel_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data mata pelajaran berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data mata pelajaran');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
    
}