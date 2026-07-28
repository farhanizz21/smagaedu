<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kepala_sekolah extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('guru_model');
		$this->load->model('mapel_model');
		$this->load->model('perangkat_model');
		$this->load->model('proyek_model');
		$this->load->model('ujian_model');
		$this->load->model('kelas_model');
		// Allow admin, kepala_sekolah, and superadmin to access
		if (!has_role(['kepala_sekolah', 'admin', 'superadmin'])) {
			show_error('Anda tidak memiliki akses ke halaman ini.', 403);
		}
	}

	public function index()
	{
		$guru = $this->guru_model->get_all();
		foreach ($guru as $val) {
			$uuid_array = json_decode($val->mapel_uuid);
			$mapel_list = $this->mapel_model->get_many_mapel_by_uuid($uuid_array);

			$val->mapel_nama = array_map(function($m) {
				return $m->nama;
			}, $mapel_list);
			
			// Get jadwal count
			$this->db->where('guru_uuid', $val->uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$val->jadwal_count = $this->db->count_all_results('jadwal_guru');
		}

		$data = array(
			'guru' => $guru,
			'active_nav' => 'kepala_sekolah'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Data Guru - Kepala Sekolah']);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']);
		$this->load->view('kepala_sekolah/kepala_sekolah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function detail($guru_uuid)
	{
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		if (!$guru) {
			show_404();
		}

		// Get mapel list
		$uuid_array = json_decode($guru->mapel_uuid);
		$mapel_list = $this->mapel_model->get_many_mapel_by_uuid($uuid_array);
		$guru->mapel_list = $mapel_list;

		// Get jadwal (schedule images)
		$this->db->where('guru_uuid', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('created_at', 'DESC');
		$jadwal = $this->db->get('jadwal_guru')->result();

		// Get perangkat for each mapel
		$perangkat_list = [];
		foreach ($mapel_list as $mapel) {
			$perangkat = $this->perangkat_model->get_perangkat_by_mapel_and_guru($mapel->uuid, $guru_uuid);
			if (!empty($perangkat)) {
				$perangkat_list[$mapel->uuid] = [
					'mapel' => $mapel,
					'perangkat' => $perangkat
				];
			}
		}

		// Get proyek (projects) created by this guru
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$proyek = $this->db->get('proyek')->result();
		
		// Add mapel nama to each proyek
		foreach ($proyek as $p) {
			$m = $this->mapel_model->get_by_uuid($p->mapel_uuid);
			$p->mapel_nama = $m ? $m->nama : '-';
		}

		// Get ujian created by this guru
		$this->db->select("u.*, m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted", FALSE);
		$this->db->from('ujian u');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->where('u.created_by', $guru_uuid);
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->order_by('u.modified_at', 'DESC');
		$ujian = $this->db->get()->result();

		// Get kelas (classes) - find classes that have students taught by this guru
		// Since there's no direct kelas-guru relationship, we'll show all classes
		$kelas = $this->kelas_model->get_all();

		$data = array(
			'guru' => $guru,
			'jadwal' => $jadwal,
			'mapel_list' => $mapel_list,
			'perangkat_list' => $perangkat_list,
			'proyek' => $proyek,
			'ujian' => $ujian,
			'kelas' => $kelas,
			'active_nav' => 'kepala_sekolah'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Detail Guru - ' . $guru->nama]);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']);
		$this->load->view('kepala_sekolah/kepala_sekolah-detail', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
}