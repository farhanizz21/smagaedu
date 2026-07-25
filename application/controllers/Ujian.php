<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		$this->load->model('siswa_model');
		$this->load->model('ujian_model');
		$this->load->model('jawaban_model');
		$this->load->model('soal_model');
	}

	public function index()
	{
		$ujian = $this->ujian_model->get_all();
		
		$user_login = $this->session->userdata('uuid'); 
		$peserta = []; 
		foreach ($ujian as $u) {
			$pengerjaan = false;
			$pengumpulan = false;
		
			$peserta[$u->uuid] = $this->siswa_model->get_by_ujian($u->uuid);
			
			foreach ($peserta[$u->uuid] as $q) {
				if ($q->ujian_uuid == $u->uuid && $q->siswa_uuid == $user_login) {
					$pengerjaan = true;
				}
		
				if ($this->ujian_model->get_pengumpulan_siswa($q->ujian_uuid, $user_login) !== NULL) {
					$pengumpulan = true;
				}
			}
		
			$u->pengumpulan = $pengumpulan;
			$u->pengerjaan = $pengerjaan;
		}
		

		$data = array(
			'ujian' => $ujian,
			'user' => $user_login,
			'peserta' => $peserta,
			'active_nav' => 'ujian'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Daftar Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah()
	{
		// Superadmin, admin, atau guru yang bisa tambah ujian
		if (!is_admin_or_superadmin() && !has_permission('manage_ujian')) {
			show_error('Anda tidak memiliki akses untuk menambah ujian.', 403);
		}
		
		$rules_ujian = $this->ujian_model->rules();
		$rules_mapel = $this->mapel_model->rules();
		$rules = array_merge($rules_ujian, $rules_mapel);
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == TRUE) {
			$insert = $this->ujian_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data ujian berhasil disimpan');
				
				$action = $this->input->post('action');
				if ($action == 'simpan') {
					redirect('ujian');
				} elseif ($action == 'simpan_detail') {
					redirect('ujian/tambah_soal/'.$insert);	
				} 
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menyimpan data ujian');
				redirect('ujian/tambah');	
			}
		
		}

		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		$mapel_list = json_decode($guru->mapel_uuid);
		$mapel = $this->mapel_model->get_many_mapel_by_uuid($mapel_list);

		$data = array(
			'mapel' => $mapel,
			'active_nav' => 'ujian'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah_sub($sub_materi_uuid = NULL)
	{
		if (empty($sub_materi_uuid) || $sub_materi_uuid == 'null') {
			redirect('ujian/tambah');
		}

		// Superadmin, admin, atau guru yang bisa tambah ujian
		if (!is_admin_or_superadmin() && !has_permission('manage_ujian')) {
			show_error('Anda tidak memiliki akses untuk menambah ujian.', 403);
		}

		$this->load->model('sub_materi_model');
		$this->load->model('bab_model');
		$sub = $this->sub_materi_model->get_by_uuid($sub_materi_uuid);
		if (empty($sub)) {
			show_404();
		}
		$bab = $this->bab_model->get_by_uuid($sub->bab_uuid);
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid);

		$rules_ujian = $this->ujian_model->rules();
		$this->form_validation->set_rules($rules_ujian);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->ujian_model->insert_sub($sub_materi_uuid);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Ujian berhasil disimpan untuk sub bab');
				
				$action = $this->input->post('action');
				if ($action == 'simpan') {
					redirect('bab/index/'.$materi->uuid);
				} elseif ($action == 'simpan_detail') {
					redirect('ujian/tambah_soal/'.$insert);	
				}
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menyimpan data ujian');
				redirect('ujian/tambah_sub/'.$sub_materi_uuid);
			}
		}

		$data = array(
			'sub' => $sub,
			'bab' => $bab,
			'materi' => $materi,
			'active_nav' => 'ujian'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Tambah Ujian Sub Bab']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
		$this->load->view('ujian/ujian-tambah-sub', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah_soal($ujian_uuid)
	{
		// Superadmin, admin, atau guru yang bisa tambah soal
		if (!is_admin_or_superadmin() && !has_permission('manage_ujian')) {
			show_error('Anda tidak memiliki akses untuk menambah soal ujian.', 403);
		}
		
		$rules = [
			[
				'field' => 'soal',
				'label' => 'Soal',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->soal_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data Soal berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Soal gagal di simpan');
			}
			redirect('ujian/tambah_soal/'.$ujian_uuid);
		}
		
		$data = array(
			'ujian' => $this->ujian_model->get_by_uuid($ujian_uuid),
			'soal' => $this->soal_model->get_by_ujian_uuid($ujian_uuid),
			'active_nav' => 'ujian'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Soal Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-soal', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah_siswa($ujian_uuid)
	{
		// Superadmin, admin, atau guru yang bisa kelola peserta
		if (!is_admin_or_superadmin() && !has_permission('manage_ujian')) {
			show_error('Anda tidak memiliki akses untuk mengelola peserta ujian.', 403);
		}
		
		$rules = [
			[
				'field' => 'siswa',
				'label' => 'siswa',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->siswa_model->insert_on_ujian();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data siswa berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data siswa gagal di simpan');
			}
			redirect('ujian/tambah_siswa/'.$ujian_uuid);
		}

		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		$peserta = $this->siswa_model->get_by_ujian($ujian_uuid);
		foreach($peserta as $p){
			$pengumpulan = $this->ujian_model->get_pengumpulan_siswa($ujian_uuid, $p->siswa_uuid);
			$p->pengumpulan = $pengumpulan ? $pengumpulan->modified_at : null;
		}
		
		$data = array(
			'ujian' => $ujian,
			'peserta' => $peserta,
			'siswa' => $this->siswa_model->get_all(),
			'active_nav' => 'ujian'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Peserta Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-siswa', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
	
	public function tambah_nilai($ujian_uuid, $siswa_uuid)
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $insert = $this->jawaban_model->insert_nilai($ujian_uuid, $siswa_uuid);
            if ($insert) {
				$this->session->set_flashdata('success_msg', 'Nilai berhasil disimpan');
            } else {
				$this->session->set_flashdata('error_msg', 'Nilai gagal disimpan');
            }
            redirect('ujian/tambah_siswa/'.$ujian_uuid	);
        }
		
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		$guru = $this->guru_model->get_by_uuid($ujian->created_by);
		$mapel = $this->mapel_model->get_by_uuid($ujian->mapel_uuid);
		$soal = $this->soal_model->get_by_ujian_uuid($ujian_uuid);
		$siswa = $this->siswa_model->get_by_uuid($siswa_uuid);
		//ambil jawaban tiap soal
		$jawaban =[];
		foreach ($soal as $d) {
			$jawaban[$d->uuid] = $this->jawaban_model->get_by_soal_uuid($d->uuid, $siswa_uuid);
		}
		if($jawaban != NULL){
			//ambil nilai total 
			$total_nilai = 0;
			$jumlah_soal = count($soal); // Menghitung jumlah soal
	
			foreach ($jawaban as $uuid => $data) {
				if (!empty($data[0]->nilai)) {
					$total_nilai += $data[0]->nilai;
				}
			}
			$rata_rata = ($jumlah_soal > 0) ? ($total_nilai / $jumlah_soal) : 0;
			$nilai_ujian = number_format($rata_rata, 2);
		}
		
		$data = array(
			'mapel' => $mapel->nama,
			'siswa' => $siswa,
			'guru' => $guru->nama,
			'ujian' => $ujian,
			'soal' => $soal,
			'nilai' => $nilai_ujian,
			'jawaban' => $jawaban,
			'active_nav' => 'ujian'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Nilai Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-nilai', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function pengerjaan($ujian_uuid)
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $insert = $this->jawaban_model->insert($ujian_uuid);
            if ($insert) {
                $this->session->set_flashdata('success_msg', 'Ujian berhasil disimpan');
            } else {
                $this->session->set_flashdata('error_msg', 'Ujian gagal disimpan');
            }
            redirect('ujian');
        }
	
		$data = array(
			'ujian' => $this->ujian_model->get_by_uuid($ujian_uuid),
			'soal' => $this->soal_model->get_by_ujian_uuid($ujian_uuid),
			'active_nav' => 'ujian'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Pengerjaan Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-pengerjaan', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus_siswa($relasi_uuid)
	{
		$result = $this->siswa_model->delete_siswa_ujian_and_ujian_jawaban_by_uuid($relasi_uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data siswa ujian berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data siswa ujian');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function hapus_soal($soal_uuid)
	{
		$result = $this->soal_model->delete_by_uuid($soal_uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data soal ujian berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data soal ujian');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function edit_soal($soal_uuid){
		
		$ujian_uuid = $this->input->post('ujian_uuid');
		$rules = [
			[
				'field' => 'soal',
				'label' => 'Soal',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->soal_model->update($soal_uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data soal berhasil di Update');
				redirect('ujian/tambah_soal/'.$ujian_uuid);
			}else {
				$this->session->set_flashdata('error_msg', 'Data soal gagal di Update');
				redirect('ujian/tambah_soal/'.$ujian_uuid);
			}
		}
	}

	public function hapus($uuid)
	{
		$result = $this->ujian_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data ujian berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data ujian');
		}
		redirect('ujian');
	}
}