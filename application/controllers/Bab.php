<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bab extends MY_Controller {

 	public function __construct()
 	{
 		parent::__construct();
 
 		$this->load->model('bab_model');
 		$this->load->model('materi_model');
 		$this->load->model('komentar_model');
 		$this->load->model('guru_model');
 		$this->load->model('siswa_model');
 		$this->load->model('sub_materi_model');
 		$this->load->model('ujian_model');
 		$this->load->model('mapel_model');
 	}

	// Daftar bab untuk satu materi
 	public function index($materi_uuid)
 	{
 		$materi = $this->materi_model->get_by_uuid($materi_uuid);
 		if (empty($materi)) {
 			show_404();
 		}
 
 		$mapel = $this->mapel_model->get_by_uuid($materi->mapel_uuid);
 		$bab = $this->bab_model->get_by_materi_uuid($materi_uuid);
 		$can_manage = is_admin_or_superadmin() || $materi->created_by == $this->session->userdata('uuid');
 
		// Get comments for each bab
  		$komentar_data = [];
  		$sub_materi_per_bab = [];
  		$ujian_per_sub = [];
  		$ujian_per_bab = [];
  		$bab_unlocked = [];
  		$bab_has_ujian = [];

  		// Untuk siswa: cek apakah ujian sub bab sebelumnya sudah dikerjakan
  		$user_role = $this->session->userdata('role');
  		$user_login = $this->session->userdata('uuid');
  		$all_unlocked = is_admin_or_superadmin() || $can_manage;

  		// Urutkan bab berdasarkan urutan (modified_at ASC)
  		$bab_sorted = $bab;

  		$prev_unlocked = true;
  		foreach ($bab_sorted as $b) {
  			$komentar = $this->komentar_model->get_by_bab_uuid($b->uuid);
  			foreach ($komentar as $kom) {
  				$guru = $this->guru_model->get_by_uuid($kom->created_by);
  				if ($guru) {
  					$kom->pengomen = $guru->nama;
  					$kom->role = 'Guru';
  				} else {
  					$siswa = $this->siswa_model->get_by_uuid($kom->created_by);
  					$kom->pengomen = $siswa ? $siswa->nama : 'Unknown';
  					$kom->role = 'Siswa';
  				}
  			}
  			$komentar_data[$b->uuid] = $komentar;
  
  			// Load sub materi for each bab
  			$sub_materi = $this->sub_materi_model->get_by_bab_uuid($b->uuid);
  			$sub_materi_per_bab[$b->uuid] = $sub_materi;
  
  			// Load ujian for each sub materi
  			foreach ($sub_materi as $sm) {
  				$ujian_per_sub[$sm->uuid] = $this->ujian_model->get_by_sub_materi($sm->uuid);
  			}

  			// Load ujian yang terhubung langsung dengan bab (dari form Tambah Sub Bab)
  			$ujian_per_bab[$b->uuid] = $this->ujian_model->get_by_bab($b->uuid);

  			// Cek apakah bab ini memiliki ujian
  			$has_ujian = !empty($ujian_per_bab[$b->uuid]);
  			$bab_has_ujian[$b->uuid] = $has_ujian;

  			// Bab saat ini terbuka jika bab sebelumnya terbuka (atau ini bab pertama)
  			$bab_unlocked[$b->uuid] = $prev_unlocked;

  			// Untuk siswa: jika bab ini terbuka dan memiliki ujian,
  			// cek apakah siswa sudah mengerjakan ujian.
  			// Jika belum selesai, bab berikutnya akan terkunci.
  			if (!$all_unlocked && $has_ujian && $bab_unlocked[$b->uuid]) {
  				$completed = false;
  				foreach ($ujian_per_bab[$b->uuid] as $u) {
  					$peserta = $this->siswa_model->get_by_ujian($u->uuid);
  					foreach ($peserta as $p) {
  						if ($p->siswa_uuid == $user_login && $this->ujian_model->get_pengumpulan_siswa($u->uuid, $user_login) !== NULL) {
  							$completed = true;
  							break 2;
  						}
  					}
  				}
  				$prev_unlocked = $completed;
  			}
  		}
  
  		$data = array(
  			'materi' => $materi,
  			'mapel' => $mapel,
  			'bab' => $bab,
  			'komentar_data' => $komentar_data,
  			'sub_materi_per_bab' => $sub_materi_per_bab,
  			'ujian_per_sub' => $ujian_per_sub,
  			'ujian_per_bab' => $ujian_per_bab,
  			'bab_unlocked' => $bab_unlocked,
  			'bab_has_ujian' => $bab_has_ujian,
  			'is_admin' => is_admin_or_superadmin(),
  			'can_manage' => $can_manage,
  			'active_nav' => 'materi'
  		);
 
 		$this->load->view('partials/header_tailwind', ['title' => 'Bab']);
 		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
         $this->load->view('mapel/sub_bab', array_merge($data, ['from_controller' => true]));
 		$this->load->view('partials/footer_tailwind');
 	}

	public function tambah($materi_uuid)
	{
		$materi = $this->materi_model->get_by_uuid($materi_uuid);
		if (empty($materi)) {
			show_404();
		}

		// Superadmin, admin, atau pengampu mata pelajaran yang boleh tambah bab
		if (!is_admin_or_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menambah bab ini.', 403);
		}

		$rules = [
			['field' => 'judul', 'label' => 'Judul Bab', 'rules' => 'required']
		];
		// Validasi ujian hanya jika guru ingin membuat ujian sekaligus
		$create_ujian = $this->input->post('create_ujian');
		if ($create_ujian == '1') {
			$rules = array_merge($rules, [
				['field' => 'namaUjian', 'label' => 'Nama Ujian', 'rules' => 'required'],
				['field' => 'jenis_penilaian', 'label' => 'Jenis Penilaian', 'rules' => 'required'],
				['field' => 'tgl_mulai', 'label' => 'Tanggal Mulai', 'rules' => 'required'],
				['field' => 'tgl_selesai', 'label' => 'Tanggal Selesai', 'rules' => 'required']
			]);
		}
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
					redirect('sub_bab/tambah/' . $materi_uuid);
				}
			}

			$bab_uuid = $this->bab_model->insert($materi_uuid, $dokumentasi);
			if ($bab_uuid) {
				$ujian_uuid = false;
				// Buat ujian jika guru memilih untuk membuat ujian sekaligus
				if ($create_ujian == '1') {
					$ujian_uuid = $this->ujian_model->insert_bab($bab_uuid, $materi->mapel_uuid);
				}

				if ($ujian_uuid) {
					$this->session->set_flashdata('success_msg', 'Sub Bab dan Ujian berhasil disimpan');
					// Jika guru memilih simpan & tambah soal, redirect ke halaman tambah soal
					$action = $this->input->post('action');
					if ($action == 'simpan_detail') {
						redirect('ujian/tambah_soal/' . $ujian_uuid);
					}
				} else {
					$this->session->set_flashdata('success_msg', 'Bab berhasil disimpan');
				}
			} else {
				$this->session->set_flashdata('error_msg', 'Bab gagal disimpan');
			}
			redirect('sub_bab/index/' . $materi_uuid);
		}

		// Ambil daftar mapel untuk dropdown (jika diperlukan di form ujian)
		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);

		$data = array(
			'materi' => $materi,
			'mapel_uuid' => $materi->mapel_uuid,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Tambah Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/sub_bab-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Buat ujian untuk sub bab (bab) yang sudah ada.
	 * Diakses dari tombol "Buat Ujian" pada halaman Index Sub Bab.
	 */
	public function tambah_ujian($bab_uuid)
	{
		$bab = $this->bab_model->get_by_uuid($bab_uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		// Superadmin, admin, atau pengampu mata pelajaran yang boleh tambah ujian
		if (!is_admin_or_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menambah ujian pada sub bab ini.', 403);
		}

		$rules = [
			['field' => 'namaUjian', 'label' => 'Nama Ujian', 'rules' => 'required'],
			['field' => 'jenis_penilaian', 'label' => 'Jenis Penilaian', 'rules' => 'required'],
			['field' => 'tgl_mulai', 'label' => 'Tanggal Mulai', 'rules' => 'required'],
			['field' => 'tgl_selesai', 'label' => 'Tanggal Selesai', 'rules' => 'required']
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$ujian_uuid = $this->ujian_model->insert_bab($bab_uuid, $materi->mapel_uuid);
			if ($ujian_uuid) {
				$this->session->set_flashdata('success_msg', 'Ujian berhasil dibuat untuk sub bab');

				$action = $this->input->post('action');
				if ($action == 'simpan_detail') {
					redirect('ujian/tambah_soal/' . $ujian_uuid);
				}
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal membuat ujian');
			}
			redirect('sub_bab/index/' . $materi->uuid);
		}

		$data = array(
			'bab' => $bab,
			'materi' => $materi,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Buat Ujian Sub Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
		$this->load->view('mapel/sub_bab-tambah-ujian', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid)
	{
		$bab = $this->bab_model->get_by_uuid($uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		// Superadmin, admin, atau pengampu mata pelajaran yang boleh edit
		if (!is_admin_or_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
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
			redirect('sub_bab/index/' . $bab->materi_uuid);
		}

		$data = array(
			'bab' => $bab,
			'materi' => $materi,
			'active_nav' => 'materi'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'materi']);
        $this->load->view('mapel/sub_bab-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function komentar_tambah($bab_uuid)
	{
        $rules = $this->komentar_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->komentar_model->insert_bab_komentar($bab_uuid);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Komentar berhasil ditambahkan');
			} else {
				$this->session->set_flashdata('error_msg', 'Komentar gagal ditambahkan');
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function komentar_hapus($uuid)
	{
		$result = $this->komentar_model->delete_bab_komentar_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Komentar berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus komentar');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function hapus($uuid)
	{
		$bab = $this->bab_model->get_by_uuid($uuid);
		if (empty($bab)) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		// Superadmin, admin, atau pengampu mata pelajaran yang boleh hapus
		if (!is_admin_or_superadmin() && $materi->created_by != $this->session->userdata('uuid')) {
			show_error('Anda tidak memiliki akses untuk menghapus bab ini.', 403);
		}

		$result = $this->bab_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Bab berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus Bab');
		}
		redirect('sub_bab/index/' . $bab->materi_uuid);
	}
}