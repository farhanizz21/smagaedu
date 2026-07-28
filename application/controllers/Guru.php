<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('guru_model');
		$this->load->model('mapel_model');
		
		// Allow guru to access jadwal methods
		$method = $this->router->fetch_method();
		if (in_array($method, ['jadwal', 'upload_jadwal', 'hapus_jadwal'])) {
			// Guru can access jadwal management
			if (!has_role(['guru', 'admin', 'superadmin'])) {
				show_error('Anda tidak memiliki akses ke halaman ini.', 403);
			}
		} else {
			$this->require_admin_or_superadmin(); // Superadmin dan admin bisa akses
		}
	}

	public function index()
	{
		$guru = $this->guru_model->get_all();
		foreach ($guru as $val) {
			$uuid_array = json_decode($val->mapel_uuid); // array UUID
			$mapel_list = $this->mapel_model->get_many_mapel_by_uuid($uuid_array); // ambil semua mapel

			$val->mapel_nama = array_map(function($m) {
				return $m->nama;
			}, $mapel_list);
		}
		

		$data = array(
			'guru' => $guru,
			'active_nav' => 'guru'
		);

		// echo "<pre>";
		// 	print_r($guru);
		// 	echo "</pre>";

        $this->load->view('partials/header_tailwind', ['title' => 'Data Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('guru/guru', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah()
	{
        $rules = $this->guru_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->guru_model->insert();
			// echo "<pre>";
			// print_r($insert);
			// echo "</pre>";
			// exit;
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data guru berhasil di simpan');
				redirect('guru');
			}else {
				$this->session->set_flashdata('error_msg', 'Data guru gagal di simpan');
				redirect('guru');
			}
		}

		$data = array(
			'mapel' => $this->mapel_model->get_all(),
			'active_nav' => 'guru'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('guru/guru-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$rules = [
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[a-z]/]|callback_username_check'
			],[
				'field' => 'namaMapel[]',
				'label' => 'Nama Mata Pelajaran',
				'rules' => 'required'
			],[
				'field' => 'jenisKelamin',
				'label' => 'Jenis Kelamin',
				'rules' => 'required'
			],
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->guru_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Guru berhasil di Update');
				redirect('guru');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Guru gagal di Update');
				redirect('guru');
			}
		}

		$guru = $this->guru_model->get_by_uuid($uuid);
		$mapel_list = json_decode($guru->mapel_uuid); // array UUID

		$data = array(
			'guru' => $guru,
			'mapel_list' => $mapel_list,
			'mapel' => $this->mapel_model->get_all(),
			'active_nav' => 'guru'
		);
		//		echo "<pre>";
		// print_r($mapel_list);
		// echo "</pre>";

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('guru/guru-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function username_check($username, $uuid)
	{
		$uuid = $this->input->post('uuid'); // atau sesuaikan dengan cara kamu ambil ID
		$this->db->where('username', $username);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where('uuid !=', $uuid);
		$this->db->where('role_id', 3);
		$query = $this->db->get('users');

		if ($query->num_rows() > 0) {
			$this->form_validation->set_message('username_check', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}
		
		return true;
	}


	public function hapus($uuid){
		{
			$result = $this->guru_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data guru berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data guru');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	// ==================== JADWAL MENGAJAR (Upload Gambar) ====================

	public function jadwal()
	{
		$guru_uuid = $this->session->userdata('uuid');
		
		$this->db->where('guru_uuid', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('created_at', 'DESC');
		$jadwal = $this->db->get('jadwal_guru')->result();

		$data = array(
			'jadwal' => $jadwal,
			'active_nav' => 'jadwal'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Jadwal Mengajar']);
		$this->load->view('partials/navbar', ['active_nav' => 'jadwal']);
		$this->load->view('guru/guru-jadwal', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function upload_jadwal()
	{
		$guru_uuid = $this->session->userdata('uuid');
		
		$this->load->library('upload');
		
		$config['upload_path'] = FCPATH . 'uploads/jadwal/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
		$config['max_size'] = 5120; // 5MB
		$config['file_name'] = 'jadwal_' . $guru_uuid . '_' . time();
		
		// Create directory if not exists
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0755, true);
		}
		
		$this->upload->initialize($config);
		
		if (!$this->upload->do_upload('file_jadwal')) {
			$this->session->set_flashdata('error_msg', 'Gagal upload jadwal: ' . $this->upload->display_errors());
			redirect('guru/jadwal');
		}
		
		$upload_data = $this->upload->data();
		$file_name = $upload_data['file_name'];
		$deskripsi = $this->input->post('deskripsi');
		
		// Use Ramsey UUID
		$uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
		
		$data = array(
			'uuid' => $uuid,
			'guru_uuid' => $guru_uuid,
			'file_gambar' => $file_name,
			'deskripsi' => $deskripsi
		);
		
		$this->db->insert('jadwal_guru', $data);
		
		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('success_msg', 'Jadwal berhasil diupload');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menyimpan jadwal');
		}
		
		redirect('guru/jadwal');
	}

	public function hapus_jadwal($uuid)
	{
		$guru_uuid = $this->session->userdata('uuid');
		
		// Verify ownership
		$this->db->where('uuid', $uuid);
		$this->db->where('guru_uuid', $guru_uuid);
		$jadwal = $this->db->get('jadwal_guru')->row();
		
		if (!$jadwal) {
			$this->session->set_flashdata('error_msg', 'Jadwal tidak ditemukan');
			redirect('guru/jadwal');
		}
		
		// Delete file
		$file_path = FCPATH . 'uploads/jadwal/' . $jadwal->file_gambar;
		if (file_exists($file_path)) {
			unlink($file_path);
		}
		
		// Soft delete record
		$this->db->where('uuid', $uuid);
		$this->db->update('jadwal_guru', ['deleted_at' => date('Y-m-d H:i:s')]);
		
		$this->session->set_flashdata('success_msg', 'Jadwal berhasil dihapus');
		redirect('guru/jadwal');
	}
}