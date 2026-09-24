<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Materi extends MY_Controller {

    public function __construct()
	{
		parent::__construct();

		$this->load->model('materi_model');
		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		$this->load->model('bab_model');
	}

	public function index()
	{
		$user_role = $this->session->userdata('role');
		$user_uuid = $this->session->userdata('uuid');
		
		// Jika guru, tampilkan mata pelajaran yang dibuat sendiri + yang diampu
		if ($user_role === 'guru') {
			$guru = $this->guru_model->get_by_uuid($user_uuid);
			$assigned_uuids = $guru->mapel_list ?? [];
			$mapel = $this->mapel_model->get_all_by_guru_relation($user_uuid, $assigned_uuids);
		} elseif ($user_role === 'siswa') {
			// Siswa hanya bisa melihat mata pelajaran sesuai kelasnya
			$this->load->model('siswa_model');
			$siswa = $this->siswa_model->get_by_uuid($user_uuid);
			$kelas_uuid = $siswa->kelas_uuid ?? null;
			$mapel = $this->mapel_model->get_all_by_kelas($kelas_uuid);
		} else {
			$mapel = $this->mapel_model->get_all();
		}

		$data = array(
			'mapel' => $mapel,
			'active_nav' => 'materi'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Daftar Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/mapel', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
    
    public function detail($mapel_uuid)
	{
		if ($this->session->userdata('role') === 'siswa') {
			$this->load->model('siswa_model');
			$siswa = $this->siswa_model->get_by_uuid($this->session->userdata('uuid'));
			$kelas_uuid = $siswa->kelas_uuid ?? null;
			$allowed_mapel = $this->mapel_model->get_mapel_uuids_by_kelas($kelas_uuid);

			if (!in_array($mapel_uuid, $allowed_mapel, true)) {
				show_error('Anda tidak memiliki akses ke mata pelajaran ini.', 403);
			}
		}

		$materi = $this->materi_model->get_by_mapel_uuid($mapel_uuid);
		$mapel = $this->mapel_model->get_by_uuid($mapel_uuid);
		$guru_uuid = $this->session->userdata('uuid');
		$pengampu = $this->guru_model->get_mapel_pengampu($mapel_uuid, $guru_uuid);
		$user_uuid = $this->session->userdata('uuid');
		$is_student = $this->session->userdata('role') === 'siswa';
		$materi_progress = $is_student ? $this->bab_model->get_progress_for_mapel($mapel_uuid, $user_uuid) : [];

		if (!empty($materi)) { // Pastikan ada data dalam materi
			foreach ($materi as $m) {
				if (!empty($m->mapel_uuid)) { // Cek apakah mapel_uuid ada
					$mapel = $this->mapel_model->get_by_uuid($m->mapel_uuid);
					$m->mapel = (!empty($mapel)) ? $mapel->nama : 'Tidak ditemukan'; // Hindari error jika null
				} else {
					$m->mapel = '-';
				}
			}
		}

		$data = array(
			'materi' => $materi,
			'mapel' => $mapel,
			'pengampu' => $pengampu,
			'materi_progress' => $materi_progress,
			'is_admin' => is_admin_or_superadmin(),
			'active_nav' => 'materi'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Daftar Materi']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/bab-list', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

    public function tambah($mapel_uuid)
	{
		// Superadmin and admin have full access, guru needs permission
		$this->require_permission('manage_materi');
		
		$rules = $this->materi_model->rules();
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$thumbnail = null;

			$config_thumbnail = array(
				'upload_path'   => "./uploads/thumbnail/",
				'allowed_types' => "jpg|png|jpeg",
				'max_size'      => 2048, // 2MB
				// 'encrypt_name'  => TRUE
			);
			$this->upload->initialize($config_thumbnail);

			if (!empty($_FILES['thumbnail']['name'])) {
				if ($this->upload->do_upload('thumbnail')) {
					$thumbnail = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah thumbnail: ' . $this->upload->display_errors());
					redirect('bab/tambah/'.$mapel_uuid);
				}
			}

			$insert = $this->materi_model->insert($thumbnail);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data Materi berhasil disimpan');
			} else {
				$this->session->set_flashdata('error_msg', 'Data Materi gagal disimpan');
			}
			redirect('mata_pelajaran/detail/'.$mapel_uuid);
		}
		
		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);

		$data = array(
			'guru' => $guru,
			'mapel_uuid' => $mapel_uuid,
			'is_admin' => is_admin_or_superadmin(),
			'active_nav' => 'materi'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Materi']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/bab-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$materi = $this->materi_model->get_by_uuid($uuid);
		if (empty($materi)) {
			show_404();
		}

		$is_admin = is_admin_or_superadmin();
		// Admin bebas edit; guru hanya untuk materi miliknya; superadmin bisa semua
		if (!$is_admin && !is_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk mengubah materi ini.', 403);
		}

		$rules = [
			[
				'field' => 'judul',
				'label' => 'Judul Materi',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

			$thumbnail = $materi->thumbnail;

			$config_thumbnail = array(
				'upload_path'   => "./uploads/thumbnail/",
				'allowed_types' => "jpg|png|jpeg",
				'max_size'      => 2048,
			);
			$this->upload->initialize($config_thumbnail);

			if (!empty($_FILES['thumbnail']['name'])) {
				if ($this->upload->do_upload('thumbnail')) {
					$thumbnail = $this->upload->data('file_name');
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah thumbnail: ' . $this->upload->display_errors());
					redirect('bab/edit/' . $uuid);
				}
			}

			$update = $this->materi_model->update($uuid, $thumbnail);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Materi berhasil di Update');
			} else {
				$this->session->set_flashdata('error_msg', 'Data Materi gagal di Update');
			}
			redirect('mata_pelajaran/detail/' . $materi->mapel_uuid);
		}

		$data = array(
			'materi' => $materi,
			'mapel_uuid' => $materi->mapel_uuid,
			'is_admin' => is_admin_or_superadmin(),
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Materi']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/bab-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		$materi = $this->materi_model->get_by_uuid($uuid);
		if (empty($materi)) {
			show_404();
		}
		// Superadmin, admin, atau pembuat materi yang boleh hapus
		if (!is_admin_or_superadmin() && !is_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menghapus materi ini.', 403);
		}

		$result = $this->materi_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data materi berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data materi');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
    
}