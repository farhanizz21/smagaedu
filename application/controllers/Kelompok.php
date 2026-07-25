<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelompok extends MY_Controller {

    public function __construct()
	{
		parent::__construct();

		$this->load->model('kelompok_model');
		$this->load->library('form_validation');
	}
	
	public function tambah()
	{
		$rules = $this->kelompok_model->rules();
		$this->form_validation->set_rules($rules);
		
		$proyek_uuid = $this->input->post('proyek_uuid'); 
		if ($this->form_validation->run() == TRUE) {
			$insert = $this->kelompok_model->insert();
			echo json_encode([
				'status' => $insert ? 'success' : 'fail'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'error' => ['kelompok' => form_error('kelompok')]
			]);
		}
	}

	public function hapus($uuid)
	{
		$proyek_uuid = $this->input->get('proyek_uuid'); 
		$result = $this->kelompok_model->delete_kelompok_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data kelompok berhasil di hapus');
		}else {
			$this->session->set_flashdata('error_msg', 'Data kelompok gagal di hapus');
		}
		redirect('proyek/pilih_siswa/'.$proyek_uuid);
	}
	
	public function tambah_siswa_by_kelompok()
	{
		$siswa_uuid = $this->input->post('siswa_uuid'); 
		$proyek_uuid = $this->input->post('proyek_uuid'); 
		$kelompok_uuid = $this->input->post('kelompok_uuid'); 
		
		if ($this->kelompok_model->is_siswa_exist($siswa_uuid, $proyek_uuid)) {
			$this->session->set_flashdata('error_msg', 'Siswa sudah ada dalam kelompok proyek ini!');
			redirect('proyek/pilih_siswa/'.$proyek_uuid);
		}
		$insert = $this->kelompok_model->insert_siswa_by_kelompok($kelompok_uuid);
		if ($insert) {
			$this->session->set_flashdata('success_msg', 'Data siswa berhasil di tambahkan');
		}else {
			$this->session->set_flashdata('error_msg', 'Data siswa gagal di tambahkan');
		}
		redirect('proyek/pilih_siswa/'.$proyek_uuid);
	}

	public function hapus_siswa_kelompok_by_relasi($relasi_uuid)
	{
		$proyek_uuid = $this->input->get('proyek_uuid');
		$result = $this->kelompok_model->delete_siswa_kelompok_by_relasi($relasi_uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data siswa berhasil di hapus');
		}else {
			$this->session->set_flashdata('error_msg', 'Data siswa gagal di hapus');
		}
		redirect('proyek/pilih_siswa/'.$proyek_uuid);
	}
}