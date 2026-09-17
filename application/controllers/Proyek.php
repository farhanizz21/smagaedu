<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proyek extends MY_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('mapel_model');
		$this->load->model('proyek_model');
		$this->load->model('guru_model');
		$this->load->model('komentar_model');
		$this->load->model('kelompok_model');
		$this->load->model('jawaban_model');
		$this->load->model('siswa_model');
	}

	public function index()
	{
		$user_login = $this->session->userdata('uuid'); 
		$user_role = $this->session->userdata('role');
		
		// Jika guru, hanya tampilkan proyek dari mata pelajaran yang dimiliki/diampu
		if ($user_role === 'guru') {
			$mapel_uuids = $this->guru_model->get_mapel_uuid_list($user_login);
			$proyek = $this->proyek_model->get_all_by_mapel_uuids($mapel_uuids);
		} elseif ($user_role === 'siswa') {
			// Siswa hanya melihat proyek dari mata pelajaran sesuai kelasnya
			$siswa = $this->siswa_model->get_by_uuid($user_login);
			$kelas_uuid = $siswa->kelas_uuid ?? null;
			$mapel_uuids = $this->mapel_model->get_mapel_uuids_by_kelas($kelas_uuid);
			$proyek = $this->proyek_model->get_all_by_mapel_uuids($mapel_uuids);
		} else {
			$proyek = $this->proyek_model->get_all();
		}
		foreach ($proyek as $py) {
			$pengerjaan = 0;
			$peserta = $this->kelompok_model->is_siswa_exist($user_login, $py->uuid);
			if(!empty($peserta)){
				$pengerjaan = 1;
			}
			$py->pengerjaan = $pengerjaan;
			
			if($py->mapel_uuid != NULL){
				$mapel = $this->mapel_model->get_by_uuid($py->mapel_uuid);
				$py->mapel = $mapel->nama;
			}
			$guru = $this->guru_model->get_by_uuid($py->created_by);
			$py->guru = $guru->nama;
		}
		
		$data = array(
			'proyek' => $proyek,
			'active_nav' => 'proyek'
		);
		
        $this->load->view('partials/header_tailwind', ['title' => 'Daftar Proyek']);
		$this->load->view('partials/navbar', ['active_nav' => 'proyek']);
        $this->load->view('proyek/proyek', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
    
    public function tambah()
	{
		// Superadmin, admin, atau guru yang bisa tambah proyek
		$this->require_permission('manage_proyek');
		
		$rules = $this->proyek_model->rules();
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run() == TRUE) {
			$this->load->library('upload');

		$config = array(
			'upload_path' => "./uploads/proyek/",
			'allowed_types' => "jpg|png|jpeg|pdf|docx|pptx",
			'max_size'      => 50000, 
			'encrypt_name'  => TRUE 
		);
			
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('berkas')) {
				$this->session->set_flashdata('error_msg', 'Gagal mengunggah berkas: ' . $this->upload->display_errors());
			}
			else {
				$data = $this->upload->data();
				$berkas = $data['file_name'];
				$insert = $this->proyek_model->insert($berkas);
				if ($insert) {
					$this->session->set_flashdata('success_msg', 'Data proyek berhasil disimpan');
					
					$action = $this->input->post('action');
					if ($action == 'simpan') {
						redirect('proyek');
					} elseif ($action == 'simpan_detail') {
						redirect('proyek/detail/'.$insert);	
					} 
				} else {
					$this->session->set_flashdata('error_msg', 'Gagal menyimpan data proyek');
					redirect('proyek/tambah');	
				}
			}
		}
		
		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		$mapel_list = $guru->mapel_list ?? [];
		$mapel = $this->mapel_model->get_many_mapel_by_uuid($mapel_list);
		
		$data = array(
			'mapel' => $mapel,
			'active_nav' => 'proyek'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Proyek']);
		$this->load->view('partials/navbar', ['active_nav' => 'proyek']);
        $this->load->view('proyek/proyek-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		{
			// Cek kepemilikan data untuk guru
			$proyek = $this->proyek_model->get_by_uuid($uuid);
			if (!$proyek) {
				show_error('Data proyek tidak ditemukan.', 404);
			}
			
			$user_role = $this->session->userdata('role');
			$user_uuid = $this->session->userdata('uuid');
			if ($user_role === 'guru' && $proyek->created_by !== $user_uuid) {
				show_error('Anda tidak memiliki akses untuk menghapus data ini.', 403);
			}
			
			$result = $this->proyek_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data proyek berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data proyek');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function detail($proyek_uuid)
	{
		$proyek = $this->proyek_model->get_by_uuid($proyek_uuid);
		if (!$proyek) {
			show_error('Data proyek tidak ditemukan.', 404);
		}
		
		// Guru hanya bisa melihat detail proyek miliknya sendiri
		$user_role = $this->session->userdata('role');
		$user_uuid = $this->session->userdata('uuid');
		if ($user_role === 'guru' && $proyek->created_by !== $user_uuid) {
			show_error('Anda tidak memiliki akses untuk melihat proyek ini.', 403);
		}
		
		$kelompok = $this->kelompok_model->get_by_proyek_uuid($proyek_uuid);
		$user_login = $this->session->userdata('uuid');
		$komentar = $this->komentar_model->get_by_proyek_uuid($proyek_uuid);
		
		// Ambil nama mata pelajaran
		if($proyek->mapel_uuid != NULL){
			$mapel = $this->mapel_model->get_by_uuid($proyek->mapel_uuid);
			$proyek->mapel = $mapel->nama;
		}
		
		$pengerjaan = false;
		$kelompok_nama = null;
		$kelompok_siswa = null;

		foreach ($kelompok as &$k) {
			$peserta = $this->siswa_model->get_by_kelompok_uuid($k['kelompok_uuid']);
			foreach ($peserta as &$s){
				if ($s->siswa_uuid == $user_login) {
					$kelompok_nama = $k['kelompok'];
					$kelompok_siswa = $k['kelompok_uuid'];
					$pengerjaan = true;
					break 2;
				}
			}
		}
		
		//cek apakah sudah mengumpulkan
		$pengumpulan = false;
		if ($this->jawaban_model->get_by_kelompok_uuid($kelompok_siswa)) { 
			$pengumpulan = true;
		}

		//cari nama pengomentar
		foreach ($komentar as $kom) {
			$nama_guru = $this->guru_model->get_by_uuid($kom->created_by);
			if (!$nama_guru){
				$nama_siswa = $this->siswa_model->get_by_uuid($kom->created_by);
				$kom->pengomen = $nama_siswa->nama;
			} else{
				$kom->pengomen = $nama_guru->nama;
			}
		}

		$data = array(
			'kelompok_nama' => $kelompok_nama,
			'kelompok_siswa' => $kelompok_siswa,
			'pengumpulan' => $pengumpulan,
			'pengerjaan' => $pengerjaan,
			'proyek' => $proyek,
			'kelompok' => $kelompok,
			'komentar' => $komentar,
			'jawaban' =>$this->jawaban_model->get_by_proyek_uuid($proyek->uuid),
			'guru' =>$this->guru_model->get_by_uuid($proyek->created_by),
			'active_nav' => 'proyek'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Detail Proyek']);
		$this->load->view('partials/navbar', ['active_nav' => 'proyek']);
        $this->load->view('proyek/proyek-detail', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function pilih_siswa($proyek_uuid)
	{
		// Superadmin, admin, atau guru yang bisa pilih siswa
		if (!is_admin_or_superadmin() && !has_permission('manage_proyek')) {
			show_error('Anda tidak memiliki akses untuk mengelola peserta proyek.', 403);
		}
		
		$proyek = $this->proyek_model->get_by_uuid($proyek_uuid);
		if (!$proyek) {
			show_error('Data proyek tidak ditemukan.', 404);
		}
		
		// Guru hanya bisa memilih siswa untuk proyek miliknya sendiri
		$user_role = $this->session->userdata('role');
		$user_uuid = $this->session->userdata('uuid');
		if ($user_role === 'guru' && $proyek->created_by !== $user_uuid) {
			show_error('Anda tidak memiliki akses untuk mengelola peserta proyek ini.', 403);
		}
		
		$kelompok = $this->kelompok_model->get_by_proyek_uuid($proyek_uuid);
		$siswa = $this->siswa_model->get_all();
		$data = array(
			'proyek' => $proyek,
			'kelompok' => $kelompok,
			'siswa' => $siswa,
			'active_nav' => 'proyek'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Pilih Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'proyek']);
        $this->load->view('proyek/proyek-pilih-siswa', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function kumpulkan($proyek_uuid)
	{
		
		$jawaban_text = $this->input->post('jawaban_text');
		$this->load->library('upload');
		
		if (!empty($_FILES['jawaban_file']['name'])) {
			// Konfigurasi upload
			$config['upload_path']   = './uploads/jawaban/'; 
			$config['allowed_types'] = 'jpg|png|jpeg|pdf|docx|pptx';  
			$config['max_size']      = 50000;
			$config['file_name']     = uniqid(); 
		
			$this->upload->initialize($config);

			if ($this->upload->do_upload('jawaban_file')) {
				$file_data = $this->upload->data();
				$jawaban_file = $file_data['file_name']; // Nama file yang tersimpan
			} else {
				$this->session->set_flashdata('error_msg', $this->upload->display_errors());
				redirect('proyek/detail/' . $proyek_uuid);
			}
		} else {
			$jawaban_file = NULL;
		}
		if (empty($jawaban_text) && empty($jawaban_file)) {
			$this->session->set_flashdata('error_msg', 'Harap isi jawaban atau upload file.');
			redirect('proyek/detail/' . $proyek_uuid);
		}

		$kumpul = $this->jawaban_model->submit_proyek_answer($proyek_uuid, $jawaban_text, $jawaban_file);
		if ($kumpul) {
			$this->session->set_flashdata('success_msg', 'Jawaban berhasil disimpan');
		} else {
			echo $kumpul;
			die;
			$this->session->set_flashdata('error_msg', 'Jawaban gagal disimpan');
		}
		redirect('proyek/detail/' . $proyek_uuid);
	}

	public function hapus_jawaban_proyek($uuid){
		{
			$result = $this->jawaban_model->delete_jawaban_proyek_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data jawaban proyek berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data jawaban proyek');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function nilai_jawaban_proyek($jawaban_uuid){
		{
			$nilai = $this->input->post('nilai_kelompok');
			
			$result = $this->jawaban_model->insert_nilai_jawaban_proyek_by_uuid($jawaban_uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Berhasil menilai jawaban proyek');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menilai jawaban proyek');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function komentar_tambah($proyek_uuid)
	{
        $rules = $this->komentar_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->komentar_model->insert($proyek_uuid);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data komentar berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data komentar gagal di simpan');
			}
			redirect('proyek/detail/'.$proyek_uuid);
		}
		redirect('proyek/detail/'.$proyek_uuid);
	}
     
}