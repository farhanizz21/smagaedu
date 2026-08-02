<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
		$user_login = $this->session->userdata('uuid'); 
		$user_role = $this->session->userdata('role');

		// Siswa hanya melihat ujian dari mata pelajaran sesuai kelasnya
		if ($user_role === 'siswa') {
			$siswa = $this->siswa_model->get_by_uuid($user_login);
			$kelas_uuid = $siswa->kelas_uuid ?? null;
			$mapel_uuids = $this->mapel_model->get_mapel_uuids_by_kelas($kelas_uuid);
			$ujian = $this->ujian_model->get_all_by_mapel_uuids($mapel_uuids);
		} elseif ($user_role === 'guru') {
			// Guru hanya melihat ujian dari mata pelajaran yang dimiliki/diampu
			$mapel_uuids = $this->guru_model->get_mapel_uuid_list($user_login);
			$ujian = $this->ujian_model->get_all_by_mapel_uuids($mapel_uuids);
		} else {
			$ujian = $this->ujian_model->get_all();
		}
		
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
		$mapel_list = $guru->mapel_list ?? [];
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
		$this->load->model('materi_model');
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

		// Ambil daftar mapel yang diampu guru untuk dropdown
		// Admin/superadmin tanpa record guru melihat semua mapel
		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		if ($guru && !empty($guru->mapel_list)) {
			$mapel = $this->mapel_model->get_many_mapel_by_uuid($guru->mapel_list);
		} else {
			$mapel = $this->mapel_model->get_all();
		}

		$data = array(
			'sub' => $sub,
			'bab' => $bab,
			'materi' => $materi,
			'mapel_list' => $mapel,
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

	/**
	 * Unduh template Excel (format soal) untuk diisi guru
	 */
	public function download_template_soal()
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Soal');

		// Header kolom
		$headers = [
			'Jenis Soal',
			'Soal',
			'Jawaban A',
			'Jawaban B',
			'Jawaban C',
			'Jawaban D',
			'Jawaban Benar',
			'Kunci 1',
			'Jawaban 1',
			'Kunci 2',
			'Jawaban 2',
			'Kunci 3',
			'Jawaban 3',
			'Kunci 4',
			'Jawaban 4',
			'Kunci 5',
			'Jawaban 5'
		];

		$sheet->fromArray($headers, NULL, 'A1');

		// Style header
		$headerStyle = [
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		];
		$sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

		// Contoh data
		$examples = [
			[
				'pilihan_ganda', 'Siapa presiden pertama Indonesia?', 'Soekarno', 'Soeharto', 'Habibie', 'Megawati', 'A', '', '', '', '', '', '', '', '', '', ''
			],
			[
				'pilihan_ganda_kompleks', 'Pilih pernyataan yang benar tentang ekosistem!', 'Terdiri dari biotik dan abiotik', 'Hanya berisi hewan', 'Mengandung rantai makanan', 'Tidak ada interaksi', 'A, C', '', '', '', '', '', '', '', '', '', ''
			],
			[
				'benar_salah', 'Air mendidih pada suhu 100°C di permukaan laut.', '', '', '', '', 'benar', '', '', '', '', '', '', '', '', '', ''
			],
			[
				'essay', 'Jelaskan proses fotosintesis secara singkat!', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
			],
			[
				'menjodohkan', 'Jodohkan organ tubuh dengan fungsinya!', '', '', '', '', '', 'Jantung', 'Memompa darah', 'Paru-paru', 'Pertukaran oksigen', 'Lambung', 'Mencerna makanan', '', '', ''
			]
		];

		$sheet->fromArray($examples, NULL, 'A2');

		// Style contoh (abukan bagian header)
		$sheet->getStyle('A2:Q6')->getFont()->setItalic(true);
		$sheet->getStyle('A2:Q6')->getFont()->getColor()->setRGB('6B7280');

		// Sheet Panduan
		$panduanSheet = $spreadsheet->createSheet();
		$panduanSheet->setTitle('Panduan');
		$panduan = [
			['PANDUAN PENGISIAN FORMAT SOAL UJIAN'],
			[''],
			['Kolom', 'Keterangan'],
			['Jenis Soal', 'pilihan_ganda | pilihan_ganda_kompleks | menjodohkan | benar_salah | essay'],
			['Soal', 'Teks soal ujian (wajib diisi)'],
			['Jawaban A - D', 'Isi untuk jenis pilihan_ganda dan pilihan_ganda_kompleks'],
			['Jawaban Benar', 'PG: A/B/C/D. PG Kompleks: A, B (pisahkan koma). Benar/Salah: benar atau salah. Essay & Menjodohkan: kosongkan.'],
			['Kunci 1-5', 'Untuk jenis menjodohkan, isi bagian kiri (pernyataan/soal)'],
			['Jawaban 1-5', 'Untuk jenis menjodohkan, isi bagian kanan (pasangan jawaban)'],
			[''],
			['CATATAN:'],
			['1. Hapus baris contoh sebelum mengisi soal Anda.'],
			['2. Satu baris = satu soal.'],
			['3. Untuk menjodohkan, maksimal 5 pasangan.'],
			['4. Baris yang kosong akan dilewati saat import.'],
			['5. File format: .xlsx']
		];
		$panduanSheet->fromArray($panduan, NULL, 'A1');
		$panduanSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$panduanSheet->getColumnDimension('A')->setWidth(20);
		$panduanSheet->getColumnDimension('B')->setWidth(80);

		// Lebar kolom pada sheet Template
		$columnWidths = ['A' => 28, 'B' => 50, 'C' => 25, 'D' => 25, 'E' => 25, 'F' => 25, 'G' => 20];
		foreach ($columnWidths as $col => $width) {
			$sheet->getColumnDimension($col)->setWidth($width);
		}
		for ($i = 8; $i <= 17; $i++) {
			$sheet->getColumnDimensionByColumn($i)->setWidth(25);
		}
		$sheet->getRowDimension(1)->setRowHeight(25);

		// Output file
		$filename = 'format_import_soal.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	/**
	 * Unduh soal yang sudah ada ke Excel
	 */
	public function export_soal($ujian_uuid)
	{
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!$ujian) {
			show_404();
		}
		$soal = $this->soal_model->get_by_ujian_uuid($ujian_uuid);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Soal Ujian');

		$headers = [
			'Jenis Soal',
			'Soal',
			'Jawaban A',
			'Jawaban B',
			'Jawaban C',
			'Jawaban D',
			'Jawaban Benar',
			'Kunci 1',
			'Jawaban 1',
			'Kunci 2',
			'Jawaban 2',
			'Kunci 3',
			'Jawaban 3',
			'Kunci 4',
			'Jawaban 4',
			'Kunci 5',
			'Jawaban 5'
		];
		$sheet->fromArray($headers, NULL, 'A1');

		$headerStyle = [
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		];
		$sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

		$row = 2;
		foreach ($soal as $s) {
			$jawaban_benar = $s->jawaban_benar;
			// Decode JSON untuk multiple answer
			$decoded = json_decode($jawaban_benar, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				$jawaban_benar = implode(', ', $decoded);
			} else {
				$jawaban_benar = $s->jawaban_benar;
			}

			$data_row = [
				$s->jenis_soal,
				$s->soal,
				$s->jawaban_a,
				$s->jawaban_b,
				$s->jawaban_c,
				$s->jawaban_d,
				$jawaban_benar
			];

			// Untuk menjodohkan, isi pasangan Kunci/Jawaban
			if ($s->jenis_soal === 'menjodohkan') {
				$pairs = $this->soal_model->get_jodohkan_pairs($s->uuid);
				$idx = 0;
				foreach ($pairs as $pair) {
					if ($idx >= 5) break;
					$data_row[] = $pair->kunci;
					$data_row[] = $pair->jawaban;
					$idx++;
				}
				// Isi kolom kosong sampai kolom Q (17)
				while (count($data_row) < 17) {
					$data_row[] = '';
				}
			}

			$sheet->fromArray($data_row, NULL, 'A' . $row);
			$row++;
		}

		$columnWidths = ['A' => 28, 'B' => 50, 'C' => 25, 'D' => 25, 'E' => 25, 'F' => 25, 'G' => 20];
		foreach ($columnWidths as $col => $width) {
			$sheet->getColumnDimension($col)->setWidth($width);
		}
		for ($i = 8; $i <= 17; $i++) {
			$sheet->getColumnDimensionByColumn($i)->setWidth(25);
		}

		$filename = 'soal_' . preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($ujian->nama)) . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	/**
	 * Import soal dari file Excel
	 */
	public function import_soal($ujian_uuid)
	{
		$ujian = $this->ujian_model->get_by_uuid($ujian_uuid);
		if (!$ujian) {
			show_404();
		}
		if (!is_admin_or_superadmin() && $this->session->userdata('uuid') != $ujian->created_by) {
			show_error('Anda tidak memiliki akses untuk mengimport soal ujian.', 403);
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			if (empty($_FILES['file_excel']['name'])) {
				$this->session->set_flashdata('error_msg', 'Pilih file Excel terlebih dahulu');
				redirect('ujian/tambah_soal/' . $ujian_uuid);
			}

			$upload_config = array(
				'upload_path' => './uploads/',
				'allowed_types' => 'xlsx|xls',
				'max_size' => 5120,
				'encrypt_name' => TRUE
			);
			$this->load->library('upload', $upload_config);

			if (!is_dir('./uploads/')) {
				mkdir('./uploads/', 0777, TRUE);
			}

			if (!$this->upload->do_upload('file_excel')) {
				$this->session->set_flashdata('error_msg', strip_tags($this->upload->display_errors()));
				redirect('ujian/tambah_soal/' . $ujian_uuid);
			}

			$upload_data = $this->upload->data();
			$file_path = './uploads/' . $upload_data['file_name'];

			try {
				$spreadsheet = IOFactory::load($file_path);
				$sheet = $spreadsheet->getSheetByName('Template Soal');
				if (!$sheet) {
					$sheet = $spreadsheet->getActiveSheet();
				}
				$rows = $sheet->toArray();

				// Buang header (baris 1)
				array_shift($rows);

				$inserted = 0;
				$errors = [];
				$no = 0; // Nomor baris asli (mulai 2 karena header di baris 1)

				foreach ($rows as $row) {
					$no++;
					$baris = $no + 1; // baris sebenarnya di excel (+1 header)

					$jenis = strtolower(trim((string)($row[0] ?? '')));
					$soal = trim((string)($row[1] ?? ''));
					$jawaban_a = trim((string)($row[2] ?? ''));
					$jawaban_b = trim((string)($row[3] ?? ''));
					$jawaban_c = trim((string)($row[4] ?? ''));
					$jawaban_d = trim((string)($row[5] ?? ''));
					$jawaban_benar = trim((string)($row[6] ?? ''));

					if ($soal === '') {
						continue; // lewati baris kosong
					}

					// Mapping jenis soal
					$jenis_map = [
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
					];

					$jenis_soal = $jenis_map[$jenis] ?? null;
					if (!$jenis_soal) {
						$errors[] = "Baris {$baris}: Jenis soal '{$jenis}' tidak dikenal. Gunakan pilihan_ganda, pilihan_ganda_kompleks, menjodohkan, benar_salah, atau essay.";
						continue;
					}

					// Validasi sesuai jenis
					if ($jenis_soal === 'pilihan_ganda' || $jenis_soal === 'pilihan_ganda_kompleks') {
						if ($jawaban_a === '' && $jawaban_b === '' && $jawaban_c === '' && $jawaban_d === '') {
							$errors[] = "Baris {$baris}: Jawaban A-D wajib diisi untuk jenis {$jenis_soal}.";
							continue;
						}
						if ($jawaban_benar === '') {
							$errors[] = "Baris {$baris}: Jawaban Benar wajib diisi untuk jenis {$jenis_soal}.";
							continue;
						}
					}

					if ($jenis_soal === 'benar_salah') {
						$jb = strtolower($jawaban_benar);
						if (!in_array($jb, ['benar', 'salah', 'true', 'false', 'b', 's'])) {
							$errors[] = "Baris {$baris}: Jawaban Benar harus 'benar' atau 'salah'.";
							continue;
						}
						$jawaban_benar = ($jb === 'benar' || $jb === 'true' || $jb === 'b') ? 'benar' : 'salah';
					}

					if ($jenis_soal === 'pilihan_ganda') {
						$jb = strtoupper($jawaban_benar);
						if (!in_array($jb, ['A', 'B', 'C', 'D'])) {
							$errors[] = "Baris {$baris}: Jawaban Benar harus A, B, C, atau D.";
							continue;
						}
						$jawaban_benar = $jb;
					}

					if ($jenis_soal === 'pilihan_ganda_kompleks') {
						$letters = array_map('trim', explode(',', $jawaban_benar));
						$letters = array_map('strtoupper', $letters);
						$valid = true;
						foreach ($letters as $l) {
							if (!in_array($l, ['A', 'B', 'C', 'D'])) {
								$valid = false;
								break;
							}
						}
						if (!$valid || empty($letters)) {
							$errors[] = "Baris {$baris}: Jawaban Benar harus kombinasi A, B, C, D dipisah koma (contoh: A, C).";
							continue;
						}
						$jawaban_benar = json_encode(array_values(array_unique($letters)));
					}

					// Siapkan data
					$soal_data = array(
						'ujian_uuid' => $ujian_uuid,
						'soal' => $soal,
						'jenis_soal' => $jenis_soal,
						'jawaban_a' => $jawaban_a ?: null,
						'jawaban_b' => $jawaban_b ?: null,
						'jawaban_c' => $jawaban_c ?: null,
						'jawaban_d' => $jawaban_d ?: null
					);

					if ($jenis_soal !== 'menjodohkan' && $jenis_soal !== 'essay') {
						$soal_data['jawaban_benar'] = $jawaban_benar;
					} else {
						$soal_data['jawaban_benar'] = null;
					}

					$jodohkan_pairs = [];
					if ($jenis_soal === 'menjodohkan') {
						// Ambil pasangan dari kolom H (index 7) sampai Q (index 16)
						for ($i = 0; $i < 5; $i++) {
							$kunci = trim((string)($row[7 + ($i * 2)] ?? ''));
							$jawaban = trim((string)($row[8 + ($i * 2)] ?? ''));
							if ($kunci !== '' && $jawaban !== '') {
								$jodohkan_pairs[] = array('kunci' => $kunci, 'jawaban' => $jawaban);
							}
						}
						if (empty($jodohkan_pairs)) {
							$errors[] = "Baris {$baris}: Minimal 1 pasangan kunci-jawaban wajib diisi untuk jenis menjodohkan.";
							continue;
						}
					}

					$insert = $this->soal_model->insert_import($soal_data, $jodohkan_pairs);
					if ($insert) {
						$inserted++;
					} else {
						$errors[] = "Baris {$baris}: Gagal menyimpan soal ke database.";
					}
				}

				// Hapus file upload
				@unlink($file_path);

				if ($inserted > 0) {
					$this->session->set_flashdata('success_msg', "{$inserted} soal berhasil diimport dari Excel.");
				}
				if (!empty($errors)) {
					$error_text = implode(' ', array_slice($errors, 0, 10));
					if (count($errors) > 10) {
						$error_text .= ' ... dan ' . (count($errors) - 10) . ' error lainnya.';
					}
					$this->session->set_flashdata('import_errors', $error_text);
				}
			} catch (\Exception $e) {
				@unlink($file_path);
				$this->session->set_flashdata('error_msg', 'Gagal membaca file Excel: ' . $e->getMessage());
			}

			redirect('ujian/tambah_soal/' . $ujian_uuid);
		}

		redirect('ujian/tambah_soal/' . $ujian_uuid);
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

		// Ambil daftar kelas sesuai mapel guru untuk dropdown
		// Jika guru, filter kelas berdasarkan kelas_map pada mapel ujian
		// Jika admin/superadmin, tampilkan semua kelas
		$guru_uuid = $this->session->userdata('uuid');
		$guru = $this->guru_model->get_by_uuid($guru_uuid);
		if ($guru && !empty($guru->kelas_map) && isset($guru->kelas_map[$ujian->mapel_uuid])) {
			$kelas_uuids = $guru->kelas_map[$ujian->mapel_uuid];
			$kelas_list = $this->kelas_model->get_by_uuids($kelas_uuids);
		} else {
			$kelas_list = $this->kelas_model->get_all();
		}

		$data = array(
			'ujian' => $ujian,
			'peserta' => $peserta,
			'kelas' => $kelas_list,
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
        $nilai_ujian = 0;
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