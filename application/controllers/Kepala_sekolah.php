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
		$this->load->model('soal_model');
		$this->load->model('siswa_model');
		// Allow admin, kepala_sekolah, and superadmin to access
		if (!has_role(['kepala_sekolah', 'admin', 'superadmin'])) {
			show_error('Anda tidak memiliki akses ke halaman ini.', 403);
		}
	}

	/**
	 * Hitung update konten (materi, bab, sub materi, ujian) yang dibuat guru
	 * untuk mata pelajaran yang diampunya.
	 *
	 * @param object $guru_obj Objek guru dari guru_model (memiliki mapel_data)
	 * @return array Berisi materi_count, bab_count, sub_materi_count, ujian_count,
	 *               mapel_updated, last_update
	 */
	private function get_mapel_update_stats($guru_obj)
	{
		$stats = array(
			'materi_count' => 0,
			'bab_count' => 0,
			'sub_materi_count' => 0,
			'ujian_count' => 0,
			'mapel_updated' => false,
			'last_update' => null
		);

		// Get mapel UUIDs assigned to this guru
		$mapel_uuids = [];
		if (!empty($guru_obj->mapel_data)) {
			foreach ($guru_obj->mapel_data as $m) {
				$mapel_uuids[] = $m->uuid;
			}
		}

		if (empty($mapel_uuids)) {
			return $stats;
		}

		$guru_uuid = $guru_obj->uuid;

		// Count materi created by this guru for their mapel
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('mapel_uuid', $mapel_uuids);
		$stats['materi_count'] = $this->db->count_all_results('materi');

		// Count ujian created by this guru for their mapel
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('mapel_uuid', $mapel_uuids);
		$stats['ujian_count'] = $this->db->count_all_results('ujian');

		// Get materi UUIDs created by this guru to count bab and sub_materi
		$this->db->select('uuid');
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('mapel_uuid', $mapel_uuids);
		$materi_rows = $this->db->get('materi')->result();
		$materi_uuids = array_column($materi_rows, 'uuid');
		$bab_uuids = [];

		if (!empty($materi_uuids)) {
			// Count bab created by this guru
			$this->db->where('created_by', $guru_uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$this->db->where_in('materi_uuid', $materi_uuids);
			$stats['bab_count'] = $this->db->count_all_results('bab');

			// Get bab UUIDs to count sub_materi
			$this->db->select('uuid');
			$this->db->where('created_by', $guru_uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$this->db->where_in('materi_uuid', $materi_uuids);
			$bab_rows = $this->db->get('bab')->result();
			$bab_uuids = array_column($bab_rows, 'uuid');

			if (!empty($bab_uuids)) {
				// Count sub_materi created by this guru
				$this->db->where('created_by', $guru_uuid);
				$this->db->where('deleted_at', NULL, FALSE);
				$this->db->where_in('bab_uuid', $bab_uuids);
				$stats['sub_materi_count'] = $this->db->count_all_results('sub_materi');
			}
		}

		// Get the latest update timestamp across all content types
		$latest_timestamps = [];

		// Latest materi update
		$this->db->select('modified_at');
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('mapel_uuid', $mapel_uuids);
		$this->db->order_by('modified_at', 'DESC');
		$this->db->limit(1);
		$latest_materi = $this->db->get('materi')->row();
		if ($latest_materi) $latest_timestamps[] = $latest_materi->modified_at;

		// Latest ujian update
		$this->db->select('modified_at');
		$this->db->where('created_by', $guru_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->where_in('mapel_uuid', $mapel_uuids);
		$this->db->order_by('modified_at', 'DESC');
		$this->db->limit(1);
		$latest_ujian = $this->db->get('ujian')->row();
		if ($latest_ujian) $latest_timestamps[] = $latest_ujian->modified_at;

		// Latest bab update
		if (!empty($materi_uuids)) {
			$this->db->select('modified_at');
			$this->db->where('created_by', $guru_uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$this->db->where_in('materi_uuid', $materi_uuids);
			$this->db->order_by('modified_at', 'DESC');
			$this->db->limit(1);
			$latest_bab = $this->db->get('bab')->row();
			if ($latest_bab) $latest_timestamps[] = $latest_bab->modified_at;
		}

		// Latest sub_materi update
		if (!empty($bab_uuids)) {
			$this->db->select('modified_at');
			$this->db->where('created_by', $guru_uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$this->db->where_in('bab_uuid', $bab_uuids);
			$this->db->order_by('modified_at', 'DESC');
			$this->db->limit(1);
			$latest_sub = $this->db->get('sub_materi')->row();
			if ($latest_sub) $latest_timestamps[] = $latest_sub->modified_at;
		}

		// Determine if teacher has made any updates
		$total_content = $stats['materi_count'] + $stats['bab_count'] + $stats['sub_materi_count'] + $stats['ujian_count'];
		$stats['mapel_updated'] = $total_content > 0;

		// Get the latest update timestamp
		if (!empty($latest_timestamps)) {
			$stats['last_update'] = max($latest_timestamps);
		}

		return $stats;
	}

	public function index()
	{
		$guru = $this->guru_model->get_all();
		foreach ($guru as $val) {
			// mapel_nama and mapel_data are already set in get_all() method
			
			// Get jadwal count
			$this->db->where('guru_uuid', $val->uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$val->jadwal_count = $this->db->count_all_results('jadwal_guru');

			// Get mapel update stats
			$stats = $this->get_mapel_update_stats($val);
			$val->materi_count = $stats['materi_count'];
			$val->bab_count = $stats['bab_count'];
			$val->sub_materi_count = $stats['sub_materi_count'];
			$val->ujian_count = $stats['ujian_count'];
			$val->mapel_updated = $stats['mapel_updated'];
			$val->last_update = $stats['last_update'];

			// Hitung total konten
			$val->total_content = $val->materi_count + $val->bab_count + $val->sub_materi_count + $val->ujian_count;
		}

		// Urutkan guru berdasarkan waktu update terakhir (terbaru ke terlama)
		usort($guru, function($a, $b) {
			$ta = $a->last_update ? strtotime($a->last_update) : 0;
			$tb = $b->last_update ? strtotime($b->last_update) : 0;
			return $tb - $ta;
		});

		// Tentukan peringkat
		$rank = 1;
		foreach ($guru as $val) {
			$val->rank = $rank++;
		}

		// Guru terbaik (3 teratas) dan perlu perhatian (3 terbawah)
		$best_guru = array_slice($guru, 0, min(3, count($guru)));
		$worst_guru = array_slice($guru, max(0, count($guru) - 3));

		$data = array(
			'guru' => $guru,
			'best_guru' => $best_guru,
			'worst_guru' => $worst_guru,
			'active_nav' => 'kepala_sekolah'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Data Guru - Kepala Sekolah']);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']);
		$this->load->view('kepala_sekolah/kepala_sekolah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function detail_ujian($ujian_uuid)
	{
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!$ujian) {
			show_404();
		}

		// Get soal for this ujian
		$soal = $this->soal_model->get_by_ujian_uuid($ujian_uuid);
		foreach ($soal as $s) {
			if ($s->jenis_soal === 'menjodohkan') {
				$s->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($s->uuid);
			}
		}

		// Get peserta (students) for this ujian
		$peserta = $this->siswa_model->get_by_ujian($ujian_uuid);
		foreach ($peserta as $p) {
			$pengumpulan = $this->ujian_model->get_pengumpulan_siswa($ujian_uuid, $p->siswa_uuid);
			$p->pengumpulan = $pengumpulan ? $pengumpulan->modified_at : null;
			$p->nilai_ujian = !empty($p->ujian_nilai) ? $p->ujian_nilai : null;
		}

		// Get guru who created this ujian
		$guru = $this->guru_model->get_by_uuid($ujian->created_by);

		$data = array(
			'ujian' => $ujian,
			'soal' => $soal,
			'peserta' => $peserta,
			'guru' => $guru,
			'active_nav' => 'kepala_sekolah'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Detail Ujian - ' . $ujian->nama]);
		$this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']);
		$this->load->view('kepala_sekolah/kepala_sekolah-detail-ujian', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function detail($guru_uuid)
	{
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		if (!$guru) {
			show_404();
		}

		// Get mapel list (already parsed in get_by_uuid)
		$mapel_list = $this->mapel_model->get_many_mapel_by_uuid($guru->mapel_list ?? []);
		$guru->mapel_list = $mapel_list;

		// Set mapel_data for update stats calculation
		$guru->mapel_data = $mapel_list;
		$update_stats = $this->get_mapel_update_stats($guru);
		$guru->materi_count = $update_stats['materi_count'];
		$guru->bab_count = $update_stats['bab_count'];
		$guru->sub_materi_count = $update_stats['sub_materi_count'];
		$guru->ujian_count = $update_stats['ujian_count'];
		$guru->mapel_updated = $update_stats['mapel_updated'];
		$guru->last_update = $update_stats['last_update'];

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