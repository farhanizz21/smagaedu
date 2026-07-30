<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel extends MY_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		// Superadmin dan admin bisa akses semua data
		// Guru hanya bisa akses data yang berelasi dengan dirinya
		if (!has_role(['admin', 'superadmin', 'guru'])) {
			show_error('Anda tidak memiliki akses ke halaman ini.', 403);
		}
	}

	public function index()
	{
		$user_uuid = $this->session->userdata('uuid');
		$user_role = $this->session->userdata('role');
		
		// Jika guru, tampilkan data yang dibuat sendiri + data yang diampu
		if ($user_role === 'guru') {
			$guru = $this->guru_model->get_by_uuid($user_uuid);
			$assigned_uuids = [];
			if ($guru && !empty($guru->mapel_uuid)) {
				$assigned_uuids = json_decode($guru->mapel_uuid, true);
			}
			$mapel = $this->mapel_model->get_all_by_guru_relation($user_uuid, $assigned_uuids);
		} else {
			$mapel = $this->mapel_model->get_all();
		}
		
		$data = array(
			'mapel' => $mapel,
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
		// Cek kepemilikan/relasi data untuk guru
		$mapel = $this->mapel_model->get_by_uuid($uuid);
		if (!$mapel) {
			show_error('Data mata pelajaran tidak ditemukan.', 404);
		}
		
		$user_role = $this->session->userdata('role');
		$user_uuid = $this->session->userdata('uuid');
		if ($user_role === 'guru') {
			$guru = $this->guru_model->get_by_uuid($user_uuid);
			$assigned_uuids = [];
			if ($guru && !empty($guru->mapel_uuid)) {
				$assigned_uuids = json_decode($guru->mapel_uuid, true);
			}
			if ($mapel->created_by !== $user_uuid && !in_array($mapel->uuid, $assigned_uuids)) {
				show_error('Anda tidak memiliki akses untuk mengedit data ini.', 403);
			}
		}
		
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
			'mapel' => $mapel,
			'active_nav' => 'mapel'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('mapel/mapel-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		{
			// Cek kepemilikan/relasi data untuk guru
			$mapel = $this->mapel_model->get_by_uuid($uuid);
			if (!$mapel) {
				show_error('Data mata pelajaran tidak ditemukan.', 404);
			}
			
			$user_role = $this->session->userdata('role');
			$user_uuid = $this->session->userdata('uuid');
			if ($user_role === 'guru') {
				$guru = $this->guru_model->get_by_uuid($user_uuid);
				$assigned_uuids = [];
				if ($guru && !empty($guru->mapel_uuid)) {
					$assigned_uuids = json_decode($guru->mapel_uuid, true);
				}
				if ($mapel->created_by !== $user_uuid && !in_array($mapel->uuid, $assigned_uuids)) {
					show_error('Anda tidak memiliki akses untuk menghapus data ini.', 403);
				}
			}
			
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