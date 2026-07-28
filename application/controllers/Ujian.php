<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		$this->load->model('siswa_model');
		$this->load->model('kelas_model');
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
					redirect('sub_bab/index/'.$materi->uuid);
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
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
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
			'ujian' => $ujian,
			'soal' => $this->soal_model->get_by_ujian_uuid($ujian_uuid),
			'active_nav' => 'ujian'
		);

		foreach ($data['soal'] as $s) {
			if ($s->jenis_soal === 'menjodohkan') {
				$s->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($s->uuid);
			}
		}

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Soal Ujian']);
		$this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-soal', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah_kelas($ujian_uuid)
	{
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
			show_error('Anda tidak memiliki akses untuk mengelola peserta ujian.', 403);
		}
		
		$rules = [
			[
				'field' => 'kelas',
				'label' => 'Kelas',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$kelas_uuid = $this->input->post('kelas');
			$insert = $this->siswa_model->insert_by_kelas($ujian_uuid, $kelas_uuid);
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data peserta berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data peserta gagal di simpan atau tidak ada siswa di kelas tersebut');
			}
			redirect('ujian/tambah_kelas/'.$ujian_uuid);
		}

		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		$peserta = $this->siswa_model->get_by_ujian($ujian_uuid);
		foreach($peserta as $p){
			$pengumpulan = $this->ujian_model->get_pengumpulan_siswa($ujian_uuid, $p->siswa_uuid);
			$p->pengumpulan = $pengumpulan ? $pengumpulan->modified_at : null;
			
			// Ambil nilai siswa jika sudah dinilai
			$p->nilai_ujian = !empty($p->ujian_nilai) ? $p->ujian_nilai : null;
		}
		
		// Ambil daftar kelas yang terdaftar sebagai peserta untuk filter
		$kelas_filter = [];
		foreach($peserta as $p){
			if(!empty($p->kelas_nama) && !in_array($p->kelas_nama, $kelas_filter)){
				$kelas_filter[] = $p->kelas_nama;
			}
		}
		sort($kelas_filter);
		
		$data = array(
			'ujian' => $ujian,
			'peserta' => $peserta,
			'kelas' => $this->kelas_model->get_all(),
			'kelas_filter' => $kelas_filter,
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
            redirect('ujian/tambah_kelas/'.$ujian_uuid	);
        }
		
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		$guru = $this->guru_model->get_by_uuid($ujian->created_by);
		$mapel = $this->mapel_model->get_by_uuid($ujian->mapel_uuid);
        $soal = $this->soal_model->get_by_ujian_uuid($ujian_uuid);
        $siswa = $this->siswa_model->get_by_uuid($siswa_uuid);
        foreach ($soal as $s) {
            if ($s->jenis_soal === 'menjodohkan') {
                $s->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($s->uuid);
            }
        }
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

        foreach ($jawaban as $soal_uuid => $data) {
            if (empty($data[0]->jawaban_siswa)) {
                continue;
            }
            $jawaban_siswa = $data[0]->jawaban_siswa;
            $soal_obj = null;
            foreach ($soal as $s) {
                if ($s->uuid === $soal_uuid) {
                    $soal_obj = $s;
                    break;
                }
            }
            if (!$soal_obj) continue;

            $display = '';
            $is_benar = null;
            switch ($soal_obj->jenis_soal) {
                case 'pilihan_ganda':
                    $prop = 'jawaban_' . strtolower($jawaban_siswa);
                    $text = $soal_obj->$prop ?? '';
                    $display = '<strong class="text-blue-700">' . htmlspecialchars($jawaban_siswa) . '</strong>. ' . htmlspecialchars($text);
                    $is_benar = strtoupper($jawaban_siswa) === strtoupper($soal_obj->jawaban_benar);
                    break;
                case 'pilihan_ganda_kompleks':
                    $arr = json_decode($jawaban_siswa, true);
                    if ($arr && is_array($arr)) {
                        sort($arr);
                        $parts = [];
                        foreach ($arr as $letter) {
                            $prop = 'jawaban_' . strtolower($letter);
                            $text = $soal_obj->$prop ?? '';
                            $parts[] = '<strong class="text-blue-700">' . htmlspecialchars($letter) . '</strong>. ' . htmlspecialchars($text);
                        }
                        $display = implode(', ', $parts);
                        $kunci = json_decode($soal_obj->jawaban_benar, true);
                        if ($kunci && is_array($kunci)) {
                            sort($kunci);
                            $is_benar = $arr === $kunci;
                        }
                    }
                    break;
                case 'menjodohkan':
                    $arr = json_decode($jawaban_siswa, true);
                    if ($arr && is_array($arr)) {
                        $pairs = $soal_obj->jodohkan_pairs ?? [];
                        $parts = [];
                        foreach ($arr as $idx => $letter) {
                            $pair = $pairs[(int)$idx - 1] ?? null;
                            if ($pair) {
                                $parts[] = htmlspecialchars($pair->kunci) . ' → <strong class="text-blue-700">' . htmlspecialchars($letter) . '</strong>. ' . htmlspecialchars($pair->jawaban);
                            }
                        }
                        $display = implode('<br>', $parts);
                    }
                    break;
                case 'benar_salah':
                    $badge_class = ($jawaban_siswa === 'benar') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
                    $display = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ' . $badge_class . '">' . htmlspecialchars(ucfirst($jawaban_siswa)) . '</span>';
                    break;
                case 'essay':
                    $display = nl2br(htmlspecialchars($jawaban_siswa));
                    break;
                default:
                    $display = htmlspecialchars($jawaban_siswa);
                    break;
            }
            if ($is_benar !== null) {
                $badge = $is_benar
                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border bg-green-100 text-green-700 border-green-200">Benar</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border bg-red-100 text-red-700 border-red-200">Salah</span>';
                $display .= ' <span class="ml-2">' . $badge . '</span>';
            }
            $data[0]->jawaban_teks = $display;
            $jawaban[$soal_uuid] = $data;
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

		foreach ($data['soal'] as $s) {
			if ($s->jenis_soal === 'menjodohkan') {
				$s->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($s->uuid);
			}
		}

        // $this->load->view('partials/header_tailwind', ['title' => 'Pengerjaan Ujian']);
		// $this->load->view('partials/navbar', ['active_nav' => 'ujian']);
        $this->load->view('ujian/ujian-pengerjaan', array_merge($data, ['from_controller' => true]));
		// $this->load->view('partials/footer_tailwind');
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
		$soal = $this->soal_model->get_by_uuid($soal_uuid);
		if ($soal) {
			$ujian = $this->ujian_model->get_by_uuid($soal->ujian_uuid);
			if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
				show_error('Anda tidak memiliki akses untuk menghapus soal ujian.', 403);
			}
		}
		$result = $this->soal_model->delete_by_uuid($soal_uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data soal ujian berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data soal ujian');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function get_soal($soal_uuid)
	{
		$soal = $this->soal_model->get_by_uuid($soal_uuid);
		if ($soal) {
			$soal->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($soal_uuid);
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'success', 'data' => $soal]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Soal tidak ditemukan']));
		}
	}

	public function get_soal_by_uuid($soal_uuid)
	{
		$soal = $this->soal_model->get_by_uuid($soal_uuid);
		if ($soal) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'success', 'data' => $soal]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['status' => 'error', 'message' => 'Soal tidak ditemukan']));
		}
	}

	public function edit_soal(){
		$soal_uuid = $this->input->post('soal_uuid');
		$ujian_uuid = $this->input->post('ujian_uuid');
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
			show_error('Anda tidak memiliki akses untuk mengedit soal ujian.', 403);
		}
		$rules = [
			[
				'field' => 'soal',
				'label' => 'Soal',
				'rules' => 'required'
			],
			[
				'field' => 'jenis_soal',
				'label' => 'Jenis Soal',
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
		$ujian = $this->ujian_model->get_by_uuid($uuid);
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
			show_error('Anda tidak memiliki akses untuk menghapus ujian.', 403);
		}
		$result = $this->ujian_model->delete_by_uuid($uuid);
		if ($result) {
			$this->session->set_flashdata('success_msg', 'Data ujian berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data ujian');
		}
		redirect('ujian');
	}

	public function bulk_hapus_soal()
	{
		$soal_uuids = $this->input->post('soal_uuids');
		$ujian_uuid = $this->input->post('ujian_uuid');

		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
			show_error('Anda tidak memiliki akses untuk menghapus soal ujian.', 403);
		}

		if (!is_array($soal_uuids) || empty($soal_uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada soal yang dipilih');
			redirect('ujian/tambah_soal/' . $ujian_uuid);
			return;
		}

		$deleted = 0;
		foreach ($soal_uuids as $uuid) {
			$result = $this->soal_model->delete_by_uuid($uuid);
			if ($result) {
				$deleted++;
			}
		}

		if ($deleted > 0) {
			$this->session->set_flashdata('success_msg', $deleted . ' soal berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus soal');
		}
		redirect('ujian/tambah_soal/' . $ujian_uuid);
	}
}