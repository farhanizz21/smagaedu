<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sub_materi extends CI_Controller {

    public function __construct()
	{
		parent::__construct();

		$this->load->model('sub_materi_model');
		$this->load->model('materi_model');
		$this->load->library('form_validation');
		$this->load->model('auth_model');
		if(!$this->auth_model->current_user()){
			redirect('login');
		}
	}

	// Tampilkan daftar sub bab untuk satu bab
	public function index($bab_uuid)
	{
		$this->load->model('bab_model');
		$bab = $this->bab_model->get_by_uuid($bab_uuid);
		if (empty($bab)) {
			show_404();
		}

		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);
		$sub = $this->sub_materi_model->get_by_bab_uuid($bab_uuid);
		$is_admin = $this->session->userdata('role') == 1;
		$can_manage = $is_admin || $materi->created_by == $this->session->userdata('uuid');

		$data = array(
			'materi' => $materi,
			'bab' => $bab,
			'sub' => $sub,
			'is_admin' => $is_admin,
			'can_manage' => $can_manage,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Sub Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/sub_materi', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah($bab_uuid)
	{
		$this->load->model('bab_model');
		$bab = $this->bab_model->get_by_uuid($bab_uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menambah sub bab ini.', 403);
		}

		$rules = $this->sub_materi_model->rules();
		$this->form_validation->set_rules($rules);

		if (empty($_FILES['berkas']['name'])) {
			$this->form_validation->set_rules('berkas', 'File Sub Bab', 'required');
		}

		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$config = array(
				'upload_path'   => "./uploads/sub_materi/",
				'allowed_types' => "jpg|png|jpeg|pdf|docx|pptx|mp4|avi|mov|mkv",
				'max_size'      => 50000,
				'encrypt_name'  => TRUE
			);
			$this->upload->initialize($config);

			if (!empty($_FILES['berkas']['name'])) {
				if ($this->upload->do_upload('berkas')) {
					$berkas = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah file: ' . $this->upload->display_errors());
					redirect('sub_materi/tambah/' . $bab_uuid);
				}
			} else {
				$berkas = null;
			}

			$insert = $this->sub_materi_model->insert_by_bab($bab_uuid, $berkas);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Sub Bab berhasil disimpan');
			} else {
				$this->session->set_flashdata('error_msg', 'Sub Bab gagal disimpan');
			}
			redirect('sub_materi/index/' . $bab_uuid);
		}

		$data = array(
			'materi' => $materi,
			'bab' => $bab,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Tambah Sub Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/sub_materi-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid)
	{
		$sub = $this->sub_materi_model->get_by_uuid($uuid);
		if (empty($sub)) {
			show_404();
		}
		$this->load->model('bab_model');
		$bab = $this->bab_model->get_by_uuid($sub->bab_uuid);
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk mengubah sub bab ini.', 403);
		}

		$rules = $this->sub_materi_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$berkas = $sub->berkas;
			$config = array(
				'upload_path'   => "./uploads/sub_materi/",
				'allowed_types' => "jpg|png|jpeg|pdf|docx|pptx|mp4|avi|mov|mkv",
				'max_size'      => 50000,
				'encrypt_name'  => TRUE
			);
			$this->upload->initialize($config);

			if (!empty($_FILES['berkas']['name'])) {
				if ($this->upload->do_upload('berkas')) {
					$berkas = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah file: ' . $this->upload->display_errors());
					redirect('sub_materi/edit/' . $uuid);
				}
			}

			$update = $this->sub_materi_model->update($uuid, $berkas);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Sub Bab berhasil di Update');
			} else {
				$this->session->set_flashdata('error_msg', 'Sub Bab gagal di Update');
			}
			redirect('sub_materi/index/' . $sub->bab_uuid);
		}

		$data = array(
			'sub' => $sub,
			'bab' => $bab,
			'materi' => $materi,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Sub Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/sub_materi-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid)
	{
		$sub = $this->sub_materi_model->get_by_uuid($uuid);
		if (empty($sub)) {
			show_404();
		}
		$this->load->model('bab_model');
		$bab = $this->bab_model->get_by_uuid($sub->bab_uuid);
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menghapus sub bab ini.', 403);
		}

		$result = $this->sub_materi_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Sub Bab berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus Sub Bab');
		}
		redirect('sub_materi/index/' . $sub->bab_uuid);
	}
}