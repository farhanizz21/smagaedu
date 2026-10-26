<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Tugas extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('tugas_model');
		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		$this->load->model('siswa_model');
		$this->load->model('soal_model');
		$this->load->model('bab_model');
		$this->load->model('materi_model');
	}

	public function index()
	{
		$role = user_role();
		$uuid = $this->session->userdata('uuid');
		$mapel_uuids = NULL;
		$pending_tasks = array();
		if ($role === 'siswa') {
			$siswa = $this->siswa_model->get_by_uuid($uuid);
			$mapel_uuids = $siswa ? $this->mapel_model->get_mapel_uuids_by_kelas($siswa->kelas_uuid) : array();
			$pending_tasks = $this->tugas_model->get_pending_by_mapel_uuids($mapel_uuids, $uuid);
		} elseif ($role === 'guru') {
			$mapel_uuids = $this->guru_model->get_mapel_uuid_list($uuid);
		}

		$per_page_options = array(10, 25, 50, 100);
		$per_page = (int) $this->input->get('per_page');
		if (!in_array($per_page, $per_page_options, TRUE)) {
			$per_page = 10;
		}
		$page = max(1, (int) $this->input->get('page'));
		$total_rows = $mapel_uuids === NULL
			? $this->tugas_model->count_all()
			: $this->tugas_model->count_by_mapel_uuids($mapel_uuids);
		$total_pages = max(1, (int) ceil($total_rows / $per_page));
		$page = min($page, $total_pages);
		$offset = ($page - 1) * $per_page;
		$tugas = $mapel_uuids === NULL
			? $this->tugas_model->get_all($per_page, $offset)
			: $this->tugas_model->get_by_mapel_uuids($mapel_uuids, $per_page, $offset);
		foreach ($tugas as $item) {
			$item->sudah_dikerjakan = $role === 'siswa'
				? $this->tugas_model->has_submission($item->uuid, $uuid)
				: FALSE;
			$item->has_student_submissions = $this->tugas_model->has_student_submissions($item->uuid);
			$item->can_manage = $this->can_manage_mapel($item->mapel_uuid, $item->created_by);
		}

		$this->load->library('pagination');
		$this->pagination->initialize(array(
			'base_url' => base_url('tugas'),
			'total_rows' => $total_rows,
			'per_page' => $per_page,
			'use_page_numbers' => TRUE,
			'page_query_string' => TRUE,
			'query_string_segment' => 'page',
			'reuse_query_string' => TRUE,
			'cur_page' => $page,
			'num_links' => 2,
			'first_link' => '&laquo;',
			'prev_link' => '&lsaquo;',
			'next_link' => '&rsaquo;',
			'last_link' => '&raquo;',
			'full_tag_open' => '<nav aria-label="Navigasi halaman"><ul class="flex flex-wrap items-center justify-center gap-1 list-none p-0 m-0">',
			'full_tag_close' => '</ul></nav>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>',
			'cur_tag_open' => '<li><span class="min-w-[38px] h-9 px-3 inline-flex items-center justify-center rounded-lg text-sm font-semibold text-white bg-blue-600 border border-blue-600">',
			'cur_tag_close' => '</span></li>',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',
			'prev_tag_open' => '<li>',
			'prev_tag_close' => '</li>',
			'next_tag_open' => '<li>',
			'next_tag_close' => '</li>',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',
			'attributes' => array(
				'class' => 'min-w-[38px] h-9 px-3 inline-flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-colors'
			)
		));
		$start_no = $total_rows > 0 ? $offset + 1 : 0;
		$end_no = min($offset + $per_page, $total_rows);

		$this->load->view('partials/header_tailwind', array('title' => 'Daftar Tugas'));
		$this->load->view('partials/navbar', array('active_nav' => 'tugas'));
		$this->load->view('tugas/index', array(
			'tugas' => $tugas,
			'pending_tasks' => $pending_tasks,
			'active_nav' => 'tugas',
			'from_controller' => TRUE,
			'per_page' => $per_page,
			'per_page_options' => $per_page_options,
			'total_rows' => $total_rows,
			'start_no' => $start_no,
			'end_no' => $end_no,
			'pagination_links' => $this->pagination->create_links()
		));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah()
	{
		$this->require_task_manager();
		$this->render_form(NULL, NULL);
	}

	public function tambah_pertemuan($bab_uuid)
	{
		$this->require_task_manager();
		$bab = $this->bab_model->get_by_uuid($bab_uuid);
		if (!$bab) {
			show_404();
		}
		$materi = $this->materi_model->get_by_uuid($bab->materi_uuid, FALSE);
		if (!$materi) {
			show_404();
		}
		if (!$this->can_manage_mapel($materi->mapel_uuid, $materi->created_by)) {
			show_error('Anda tidak memiliki akses untuk menambah tugas pada pertemuan ini.', 403);
		}
		$this->render_form($bab, NULL);
	}

	public function edit($tugas_uuid)
	{
		$this->require_task_manager();
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		if ($this->tugas_model->has_student_submissions($tugas_uuid)) {
			$this->session->set_flashdata('error_msg', 'Tugas tidak dapat diedit karena sudah ada siswa yang mengumpulkan jawaban.');
			redirect('tugas');
		}
		$bab = NULL;
		if (!empty($tugas->bab_uuid)) {
			$bab = $this->bab_model->get_by_uuid($tugas->bab_uuid);
		}
		$this->render_form($bab, $tugas);
	}

	private function render_form($bab, $tugas)
	{
		$mapel_uuid = $tugas ? $tugas->mapel_uuid : NULL;
		if ($bab && !$tugas) {
			$materi = $this->materi_model->get_by_uuid($bab->materi_uuid, FALSE);
			$mapel_uuid = $materi->mapel_uuid;
		}
		$rules = $this->tugas_model->rules();
		$rules[] = array('field' => 'mapel_uuid', 'label' => 'Mata Pelajaran', 'rules' => 'required');
		$rules[2]['rules'] .= '|callback_valid_tugas_date_range';
		$this->form_validation->set_rules($rules);
		$is_valid = $this->input->method(TRUE) === 'POST' && $this->form_validation->run() === TRUE;
		if ($is_valid) {
			$mapel_terkunci = $bab || ($tugas && !empty($tugas->bab_uuid));
			$posted_mapel = $mapel_terkunci ? $mapel_uuid : $this->input->post('mapel_uuid');
			$mapel = $this->mapel_model->get_by_uuid($posted_mapel, FALSE);
			if (!$mapel || $mapel->deleted_at !== NULL || !$this->can_manage_mapel($posted_mapel, $mapel->created_by)) {
				show_error('Anda tidak memiliki akses ke mata pelajaran ini.', 403);
			}

			if ($tugas) {
				if ($this->tugas_model->has_student_submissions($tugas->uuid)) {
					$this->session->set_flashdata('error_msg', 'Tugas tidak dapat diedit karena sudah ada siswa yang mengumpulkan jawaban.');
					redirect('tugas');
				}
				$saved = $this->tugas_model->update($tugas->uuid, $posted_mapel);
				if ($saved) {
					$this->session->set_flashdata('success_msg', 'Tugas berhasil diperbarui.');
					redirect('tugas');
				}
				$this->session->set_flashdata('error_msg', 'Tanggal selesai harus setelah tanggal mulai atau tugas gagal diperbarui.');
			} else {
				$inserted = $this->tugas_model->insert($posted_mapel, $bab ? $bab->uuid : NULL);
				if ($inserted) {
					$this->session->set_flashdata('success_msg', 'Tugas berhasil disimpan.');
					redirect('tugas/tambah_soal/' . $inserted);
				}
				$this->session->set_flashdata('error_msg', 'Tanggal selesai harus setelah tanggal mulai atau tugas gagal disimpan.');
			}
		}

		$mapel = $this->available_mapel();
		$selected_mapel = $mapel_uuid ? $this->mapel_model->get_by_uuid($mapel_uuid, FALSE) : NULL;
		$this->load->view('partials/header_tailwind', array('title' => $tugas ? 'Edit Tugas' : 'Tambah Tugas'));
		$this->load->view('partials/navbar', array('active_nav' => 'tugas'));
		$this->load->view('tugas/tambah', array(
			'mapel' => $mapel,
			'selected_mapel' => $selected_mapel,
			'mapel_uuid' => $mapel_uuid,
			'tugas' => $tugas,
			'bab' => $bab,
			'active_nav' => 'tugas',
			'from_controller' => TRUE
		));
		$this->load->view('partials/footer_tailwind');
	}

	public function valid_tugas_date_range($tgl_selesai)
	{
		$start_value = (string) $this->input->post('tgl_mulai');
		$start = DateTime::createFromFormat('!Y-m-d\TH:i', $start_value);
		$start_errors = DateTime::getLastErrors();
		$end = DateTime::createFromFormat('!Y-m-d\TH:i', (string) $tgl_selesai);
		$end_errors = DateTime::getLastErrors();
		if (!$start || !$end
			|| ($start_errors && ($start_errors['warning_count'] || $start_errors['error_count']))
			|| ($end_errors && ($end_errors['warning_count'] || $end_errors['error_count']))
			|| $end <= $start) {
			$this->form_validation->set_message('valid_tugas_date_range', 'Tanggal selesai harus setelah tanggal mulai dan menggunakan format yang benar.');
			return FALSE;
		}
		return TRUE;
	}

	public function tambah_soal($tugas_uuid)
	{
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$has_student_submissions = $this->tugas_model->has_student_submissions($tugas_uuid);
		if ($has_student_submissions && $this->input->method(TRUE) === 'POST') {
			$this->session->set_flashdata('error_msg', 'Soal tidak dapat ditambahkan karena tugas sudah dikerjakan oleh siswa.');
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}
		$this->form_validation->set_rules($this->soal_model->rules());
		if ($this->input->method(TRUE) === 'POST' && $this->form_validation->run() === TRUE) {
			$inserted = $this->soal_model->insert($tugas_uuid);
			$this->session->set_flashdata($inserted ? 'success_msg' : 'error_msg', $inserted ? 'Soal tugas berhasil disimpan.' : 'Soal tugas gagal disimpan.');
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}
		$soal = $this->soal_model->get_by_tugas_uuid($tugas_uuid);
		foreach ($soal as $item) {
			if ($item->jenis_soal === 'menjodohkan') {
				$item->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($item->uuid);
			}
		}
		$this->load->view('partials/header_tailwind', array('title' => 'Tambah Soal Tugas'));
		$this->load->view('partials/navbar', array('active_nav' => 'tugas'));
		$this->load->view('ujian/ujian-soal', array(
			'ujian' => (object) array('uuid' => $tugas->uuid, 'nama' => $tugas->judul),
			'soal' => $soal,
			'is_tugas' => TRUE,
			'has_student_submissions' => $has_student_submissions,
			'active_nav' => 'tugas',
			'from_controller' => TRUE
		));
		$this->load->view('partials/footer_tailwind');
	}

	public function import_soal($tugas_uuid)
	{
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		if ($this->tugas_model->has_student_submissions($tugas_uuid)) {
			$this->session->set_flashdata('error_msg', 'Soal tidak dapat diimpor karena tugas sudah dikerjakan oleh siswa.');
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}
		if ($this->input->method(TRUE) !== 'POST') {
			show_error('Metode permintaan tidak valid.', 405);
		}
		if (empty($_FILES['file_excel']['name'])) {
			$this->session->set_flashdata('error_msg', 'Pilih file Excel terlebih dahulu.');
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}

		if (!is_dir('./uploads/') && !mkdir('./uploads/', 0775, TRUE) && !is_dir('./uploads/')) {
			show_error('Folder unggahan tidak dapat dibuat.', 500);
		}
		$this->load->library('upload');
		$this->upload->initialize(array(
			'upload_path' => './uploads/',
			'allowed_types' => 'xlsx|xls',
			'max_size' => 5120,
			'encrypt_name' => TRUE
		));
		if (!$this->upload->do_upload('file_excel')) {
			$this->session->set_flashdata('error_msg', strip_tags($this->upload->display_errors()));
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}

		$file_path = $this->upload->data('full_path');
		try {
			$spreadsheet = IOFactory::load($file_path);
			$sheet = $spreadsheet->getSheetByName('Template Soal') ?: $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			array_shift($rows);

			$jenis_map = array(
				'pilihan_ganda' => 'pilihan_ganda',
				'pg' => 'pilihan_ganda',
				'pilihan ganda' => 'pilihan_ganda',
				'pilgan' => 'pilihan_ganda',
				'pilihan_ganda_kompleks' => 'pilihan_ganda_kompleks',
				'pg kompleks' => 'pilihan_ganda_kompleks',
				'pgk' => 'pilihan_ganda_kompleks',
				'kompleks' => 'pilihan_ganda_kompleks',
				'benar_salah' => 'benar_salah',
				'benar atau salah' => 'benar_salah',
				'benar/salah' => 'benar_salah',
				'bs' => 'benar_salah',
				'essay' => 'essay',
				'esai' => 'essay',
				'uraian' => 'essay',
				'isian' => 'essay',
				'menjodohkan' => 'menjodohkan',
				'jodohkan' => 'menjodohkan',
				'matching' => 'menjodohkan'
			);
			$inserted = 0;
			$errors = array();
			foreach ($rows as $index => $row) {
				$row_number = $index + 2;
				$jenis = strtolower(trim((string) ($row[0] ?? '')));
				$soal = trim((string) ($row[1] ?? ''));
				if ($soal === '') {
					continue;
				}
				$jenis_soal = $jenis_map[$jenis] ?? NULL;
				if (!$jenis_soal) {
					$errors[] = "Baris {$row_number}: jenis soal tidak dikenal.";
					continue;
				}

				$options = array();
				foreach (array('a', 'b', 'c', 'd', 'e') as $option_index => $letter) {
					$options[$letter] = trim((string) ($row[$option_index + 2] ?? ''));
				}
				$jawaban_benar = trim((string) ($row[7] ?? ''));
				$has_option = count(array_filter($options, function ($option) {
					return $option !== '';
				})) > 0;
				if (in_array($jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)
					&& (!$has_option || $jawaban_benar === '')) {
					$errors[] = "Baris {$row_number}: pilihan jawaban dan jawaban benar wajib diisi.";
					continue;
				}
				if ($jenis_soal === 'pilihan_ganda') {
					$jawaban_benar = strtoupper($jawaban_benar);
					if (!in_array($jawaban_benar, array('A', 'B', 'C', 'D', 'E'), TRUE)) {
						$errors[] = "Baris {$row_number}: jawaban benar harus A, B, C, D, atau E.";
						continue;
					}
				} elseif ($jenis_soal === 'pilihan_ganda_kompleks') {
					$letters = array_map('strtoupper', array_map('trim', explode(',', $jawaban_benar)));
					if (!$letters || array_diff($letters, array('A', 'B', 'C', 'D', 'E'))) {
						$errors[] = "Baris {$row_number}: jawaban benar harus berupa kombinasi A-E yang dipisahkan koma.";
						continue;
					}
					$jawaban_benar = json_encode(array_values(array_unique($letters)));
				} elseif ($jenis_soal === 'benar_salah') {
					$normalized = strtolower($jawaban_benar);
					if (!in_array($normalized, array('benar', 'salah', 'true', 'false', 'b', 's'), TRUE)) {
						$errors[] = "Baris {$row_number}: jawaban benar harus benar atau salah.";
						continue;
					}
					$jawaban_benar = in_array($normalized, array('benar', 'true', 'b'), TRUE) ? 'benar' : 'salah';
				}

				$pairs = array();
				if ($jenis_soal === 'menjodohkan') {
					for ($pair_index = 0; $pair_index < 5; $pair_index++) {
						$key = trim((string) ($row[8 + ($pair_index * 2)] ?? ''));
						$answer = trim((string) ($row[9 + ($pair_index * 2)] ?? ''));
						if ($key !== '' && $answer !== '') {
							$pairs[] = array('kunci' => $key, 'jawaban' => $answer);
						}
					}
					if (!$pairs) {
						$errors[] = "Baris {$row_number}: minimal satu pasangan wajib diisi untuk soal menjodohkan.";
						continue;
					}
				}

				$question_data = array(
					'ujian_uuid' => $tugas_uuid,
					'tugas_uuid' => $tugas_uuid,
					'soal' => $soal,
					'jenis_soal' => $jenis_soal,
					'jenis_jawaban_essay' => $jenis_soal === 'essay' ? 'teks' : NULL,
					'jawaban_a' => $options['a'] ?: NULL,
					'jawaban_b' => $options['b'] ?: NULL,
					'jawaban_c' => $options['c'] ?: NULL,
					'jawaban_d' => $options['d'] ?: NULL,
					'jawaban_e' => $options['e'] ?: NULL,
					'jawaban_benar' => in_array($jenis_soal, array('menjodohkan', 'essay'), TRUE) ? NULL : $jawaban_benar
				);
				if ($this->soal_model->insert_import($question_data, $pairs)) {
					$inserted++;
				} else {
					$errors[] = "Baris {$row_number}: gagal menyimpan soal.";
				}
			}
			if ($inserted > 0) {
				$this->session->set_flashdata('success_msg', $inserted . ' soal berhasil diimport dari Excel.');
			}
			if ($errors) {
				$error_text = implode(' ', array_slice($errors, 0, 10));
				if (count($errors) > 10) {
					$error_text .= ' ... dan ' . (count($errors) - 10) . ' error lainnya.';
				}
				$this->session->set_flashdata('import_errors', $error_text);
			} elseif ($inserted === 0) {
				$this->session->set_flashdata('error_msg', 'Tidak ada soal valid yang ditemukan pada file Excel.');
			}
		} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
			$this->session->set_flashdata('error_msg', 'Gagal membaca file Excel: ' . $e->getMessage());
		} finally {
			if (is_file($file_path)) {
				unlink($file_path);
			}
		}
		redirect('tugas/tambah_soal/' . $tugas_uuid);
	}

	public function pengumpulan($tugas_uuid)
	{
		$this->require_task_manager();
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$submissions = array();
		foreach ($this->tugas_model->get_submission_answers($tugas_uuid) as $answer) {
			if (!isset($submissions[$answer->siswa_uuid])) {
				$submissions[$answer->siswa_uuid] = array(
					'nama' => $answer->siswa_nama,
					'nis' => $answer->nis,
					'kelas_nama' => $answer->kelas_nama,
					'terakhir_diubah' => $answer->jawaban_diubah,
					'jawaban' => array()
				);
			} elseif ($answer->jawaban_diubah > $submissions[$answer->siswa_uuid]['terakhir_diubah']) {
				$submissions[$answer->siswa_uuid]['terakhir_diubah'] = $answer->jawaban_diubah;
			}
			$submissions[$answer->siswa_uuid]['jawaban'][] = $answer;
		}
		$this->load->view('partials/header_tailwind', array('title' => 'Pengumpulan Tugas'));
		$this->load->view('partials/navbar', array('active_nav' => 'tugas'));
		$this->load->view('tugas/pengumpulan', array(
			'tugas' => $tugas,
			'submissions' => $submissions,
			'active_nav' => 'tugas',
			'from_controller' => TRUE
		));
		$this->load->view('partials/footer_tailwind');
	}

	public function kerjakan($tugas_uuid)
	{
		if (!has_role('siswa')) {
			show_error('Halaman pengerjaan tugas hanya tersedia untuk siswa.', 403);
		}
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$siswa_uuid = $this->session->userdata('uuid');
		$siswa = $this->siswa_model->get_by_uuid($siswa_uuid);
		$mapel_siswa = $siswa ? $this->mapel_model->get_mapel_uuids_by_kelas($siswa->kelas_uuid) : array();
		if (!$siswa || !in_array($tugas->mapel_uuid, $mapel_siswa, TRUE)) {
			show_error('Anda tidak memiliki akses ke tugas ini.', 403);
		}

		$has_submission = $this->tugas_model->has_submission($tugas_uuid, $siswa_uuid);
		if ($this->input->method(TRUE) === 'POST') {
			if ($has_submission) {
				$this->session->set_flashdata('error_msg', 'Jawaban yang sudah dikumpulkan hanya dapat dilihat dan tidak dapat diubah.');
				redirect('tugas');
			}
			$answers = $this->input->post('jawaban');
			$answers = is_array($answers) ? $answers : array();
			foreach (array_keys($answers) as $soal_uuid) {
				$question = $this->soal_model->get_by_uuid($soal_uuid);
				if ($question && $question->tugas_uuid === $tugas_uuid
					&& $question->jenis_soal === 'essay'
					&& $question->jenis_jawaban_essay === 'file') {
					unset($answers[$soal_uuid]);
				}
			}
			$files = $_FILES['jawaban_file'] ?? array();
			foreach ((array) ($files['name'] ?? array()) as $soal_uuid => $filename) {
				if (empty($filename)) {
					continue;
				}
				foreach (array('type', 'tmp_name', 'error', 'size') as $file_key) {
					if (!isset($files[$file_key][$soal_uuid])) {
						show_error('Data unggahan jawaban tidak valid.', 400);
					}
				}
				$question = $this->soal_model->get_by_uuid($soal_uuid);
				if (!$question || $question->tugas_uuid !== $tugas_uuid
					|| $question->jenis_soal !== 'essay'
					|| $question->jenis_jawaban_essay !== 'file') {
					show_error('Unggahan tidak sesuai dengan soal tugas.', 400);
				}
				$upload_path = FCPATH . 'uploads/jawaban_tugas/';
				if (!is_dir($upload_path) && !mkdir($upload_path, 0775, TRUE) && !is_dir($upload_path)) {
					show_error('Folder penyimpanan jawaban tugas tidak dapat dibuat.', 500);
				}
				$this->load->library('upload');
				$this->upload->initialize(array(
					'upload_path' => $upload_path,
					'allowed_types' => 'pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png',
					'max_size' => 10240,
					'encrypt_name' => TRUE
				));
				$_FILES['jawaban_tugas_upload'] = array(
					'name' => $files['name'][$soal_uuid],
					'type' => $files['type'][$soal_uuid],
					'tmp_name' => $files['tmp_name'][$soal_uuid],
					'error' => $files['error'][$soal_uuid],
					'size' => $files['size'][$soal_uuid]
				);
				if (!$this->upload->do_upload('jawaban_tugas_upload')) {
					$this->session->set_flashdata('error_msg', 'Gagal mengunggah jawaban: ' . strip_tags($this->upload->display_errors()));
					redirect('tugas/kerjakan/' . $tugas_uuid);
				}
				$answers[$soal_uuid] = $this->upload->data('file_name');
			}
			$saved = $this->tugas_model->save_answers($tugas_uuid, $siswa_uuid, $answers);
			$this->session->set_flashdata($saved ? 'success_msg' : 'error_msg', $saved ? 'Jawaban tugas berhasil disimpan.' : 'Jawaban tugas gagal disimpan.');
			redirect($saved ? 'tugas' : 'tugas/kerjakan/' . $tugas_uuid);
		}
		$soal = $this->soal_model->get_by_tugas_uuid($tugas_uuid);
		foreach ($soal as $item) {
			if ($item->jenis_soal === 'menjodohkan') {
				$item->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($item->uuid);
			}
		}
		$this->load->view('partials/header_tailwind', array('title' => 'Kerjakan Tugas'));
		$this->load->view('partials/navbar', array('active_nav' => 'tugas'));
		$answers = $this->tugas_model->get_answers($tugas_uuid, $siswa_uuid);
		$this->load->view('tugas/kerjakan', array(
			'tugas' => $tugas,
			'soal' => $soal,
			'answers' => $answers,
			'read_only' => $has_submission,
			'active_nav' => 'tugas',
			'from_controller' => TRUE
		));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus_soal($soal_uuid)
	{
		$soal = $this->soal_model->get_by_uuid($soal_uuid);
		if (!$soal || empty($soal->tugas_uuid)) {
			show_404();
		}
		$tugas = $this->tugas_model->get_by_uuid($soal->tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$deleted = $this->soal_model->delete_by_uuid($soal_uuid);
		$this->session->set_flashdata($deleted ? 'success_msg' : 'error_msg', $deleted ? 'Soal tugas berhasil dihapus.' : 'Gagal menghapus soal tugas.');
		redirect('tugas/tambah_soal/' . $tugas->uuid);
	}

	public function get_soal($soal_uuid)
	{
		$soal = $this->soal_model->get_by_uuid($soal_uuid);
		$tugas = $soal && !empty($soal->tugas_uuid) ? $this->tugas_model->get_by_uuid($soal->tugas_uuid) : NULL;
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$soal->jodohkan_pairs = $this->soal_model->get_jodohkan_pairs($soal_uuid);
		$this->output->set_content_type('application/json')->set_output(json_encode(array('status' => 'success', 'data' => $soal)));
	}

	public function edit_soal()
	{
		$tugas_uuid = $this->input->post('tugas_uuid');
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$soal = $this->soal_model->get_by_uuid($this->input->post('soal_uuid'));
		if (!$soal || $soal->tugas_uuid !== $tugas_uuid) {
			show_404();
		}
		if ($this->tugas_model->has_student_submissions($tugas_uuid)
			&& $this->input->post('confirm_submitted') !== '1') {
			$this->session->set_flashdata('error_msg', 'Perubahan soal dibatalkan. Tugas sudah memiliki jawaban siswa.');
			redirect('tugas/tambah_soal/' . $tugas_uuid);
		}
		$this->form_validation->set_rules($this->soal_model->rules());
		$updated = $this->form_validation->run() === TRUE && $this->soal_model->update($soal->uuid);
		$this->session->set_flashdata($updated ? 'success_msg' : 'error_msg', $updated ? 'Soal tugas berhasil diperbarui.' : 'Soal tugas gagal diperbarui.');
		redirect('tugas/tambah_soal/' . $tugas_uuid);
	}

	public function bulk_hapus_soal()
	{
		$tugas_uuid = $this->input->post('tugas_uuid');
		$tugas = $this->tugas_model->get_by_uuid($tugas_uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		$uuids = json_decode($this->input->post('soal_uuids'), TRUE);
		$deleted = 0;
		foreach ((array) $uuids as $uuid) {
			$soal = $this->soal_model->get_by_uuid($uuid);
			if ($soal && $soal->tugas_uuid === $tugas_uuid && $this->soal_model->delete_by_uuid($uuid)) {
				$deleted++;
			}
		}
		$this->session->set_flashdata($deleted ? 'success_msg' : 'error_msg', $deleted ? $deleted . ' soal berhasil dihapus.' : 'Tidak ada soal yang berhasil dihapus.');
		redirect('tugas/tambah_soal/' . $tugas_uuid);
	}

	public function hapus($uuid)
	{
		$tugas = $this->tugas_model->get_by_uuid($uuid);
		if (!$tugas) {
			show_404();
		}
		$this->require_tugas_owner($tugas);
		if ($this->tugas_model->has_student_submissions($uuid)) {
			$this->session->set_flashdata('error_msg', 'Tugas tidak dapat dihapus karena sudah ada siswa yang mengerjakan.');
			redirect('tugas');
		}
		$deleted = $this->tugas_model->delete_by_uuid($uuid);
		$this->session->set_flashdata($deleted ? 'success_msg' : 'error_msg', $deleted ? 'Tugas berhasil dihapus.' : 'Gagal menghapus tugas.');
		redirect('tugas');
	}

	private function available_mapel()
	{
		if (has_role(['admin', 'superadmin'])) {
			return $this->mapel_model->get_all();
		}
		$guru = $this->guru_model->get_by_uuid($this->session->userdata('uuid'));
		return $guru ? $this->mapel_model->get_many_mapel_by_uuid($guru->mapel_list ?? array()) : array();
	}

	private function can_manage_mapel($mapel_uuid, $creator_uuid)
	{
		return admin_can_access_creator($creator_uuid)
			|| (has_role('guru') && $this->guru_model->get_mapel_pengampu($mapel_uuid, $this->session->userdata('uuid')));
	}

	private function require_task_manager()
	{
		if (!is_admin_or_superadmin() && !has_permission('manage_ujian')) {
			show_error('Anda tidak memiliki akses untuk mengelola tugas.', 403);
		}
	}

	private function require_tugas_owner($tugas)
	{
		if (!$this->can_manage_mapel($tugas->mapel_uuid, $tugas->created_by)) {
			show_error('Anda tidak memiliki akses untuk mengelola tugas ini.', 403);
		}
	}
}
