<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bab extends CI_Controller {

    public function __construct()
	{
		parent::__construct();

		$this->load->model('bab_model');
		$this->load->model('materi_model');
		$this->load->library('form_validation');
		$this->load->model('auth_model');
		if(!$this->auth_model->current_user()){
			redirect('login');
		}
	}

	// Daftar bab untuk satu materi
	public function index($materi_uuid)
	{
		$materi = $this->materi_model->get_by_uuid($materi_uuid);
		if (empty($materi)) {
			show_404();
		}

		$bab = $this->bab_model->get_by_materi_uuid($materi_uuid);
		$is_admin = $this->session->userdata('role') == 1;
		$can_manage = $is_admin || $materi->created_by == $this->session->userdata('uuid');

		$data = array(
			'materi' => $materi,
			'bab' => $bab,
			'is_admin' => $is_admin,
			'can_manage' => $can_manage,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/bab', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah($materi_uuid)
	{
		$materi = $this->materi_model->get_by_uuid($materi_uuid);
		if (empty($materi)) {
			show_404();
		}

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menambah bab ini.', 403);
		}

		$rules = [
			['field' => 'judul', 'label' => 'Judul Bab', 'rules' => 'required']
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$dokumentasi = null;

			$config_dokumentasi = array(
				'upload_path'   => FCPATH . "uploads/dokumentasi/",
				'allowed_types' => "pdf|docx|pptx",
				'max_size'      => 50000,
				'encrypt_name'  => TRUE
			);
			$this->upload->initialize($config_dokumentasi);

			if (!empty($_FILES['dokumentasi']['name'])) {
				if ($this->upload->do_upload('dokumentasi')) {
					$dokumentasi = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah dokumentasi: ' . $this->upload->display_errors());
					redirect('bab/tambah/' . $materi_uuid);
				}
			}

			if ($this->bab_model->insert($materi_uuid, $dokumentasi)) {
				$this->session->set_flashdata('success_msg', 'Bab berhasil disimpan');
			} else {
				$this->session->set_flashdata('error_msg', 'Bab gagal disimpan');
			}
			redirect('bab/index/' . $materi_uuid);
		}

		$data = array(
			'materi' => $materi,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Tambah Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/bab-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid)
	{
		$bab = $this->bab_model->get_by_uuid($uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk mengubah bab ini.', 403);
		}

		$rules = [
			['field' => 'judul', 'label' => 'Judul Bab', 'rules' => 'required']
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$dokumentasi = $bab->dokumentasi;

			$config_dokumentasi = array(
				'upload_path'   => FCPATH . "uploads/dokumentasi/",
				'allowed_types' => "pdf|docx|pptx",
				'max_size'      => 50000,
				'encrypt_name'  => TRUE
			);
			$this->upload->initialize($config_dokumentasi);

			if (!empty($_FILES['dokumentasi']['name'])) {
				if ($this->upload->do_upload('dokumentasi')) {
					$dokumentasi = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah dokumentasi: ' . $this->upload->display_errors());
					redirect('bab/edit/' . $uuid);
				}
			}

			if ($this->bab_model->update($uuid, $dokumentasi)) {
				$this->session->set_flashdata('success_msg', 'Bab berhasil di Update');
			} else {
				$this->session->set_flashdata('error_msg', 'Bab gagal di Update');
			}
			redirect('bab/index/' . $bab->materi_uuid);
		}

		$data = array(
			'bab' => $bab,
			'materi' => $materi,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('materi/bab-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid)
	{
		$bab = $this->bab_model->get_by_uuid($uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$is_admin = $this->session->userdata('role') == 1;
		if (!$is_admin && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menghapus bab ini.', 403);
		}

		$result = $this->bab_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Bab berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus Bab');
		}
		redirect('bab/index/' . $bab->materi_uuid);
	}
}