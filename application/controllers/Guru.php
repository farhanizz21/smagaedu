<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class Guru extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('guru_model');
		$this->load->model('mapel_model');
		$this->load->model('kelas_model');

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
		// Data is already enriched with mapel_nama, mapel_data, and kelas_per_mapel in the model

		$data = array(
			'guru' => $guru,
			'active_nav' => 'guru'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Data Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('master/guru/guru', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function tambah()
	{
        $rules = $this->guru_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->guru_model->insert();
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
			'kelas' => $this->kelas_model->get_all(),
			'active_nav' => 'guru'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('master/guru/guru-tambah', array_merge($data, ['from_controller' => true]));
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

		$data = array(
			'guru' => $guru,
			'mapel_list' => $guru->mapel_list ?? [],
			'kelas_map' => $guru->kelas_map ?? [],
			'mapel' => $this->mapel_model->get_all(),
			'kelas' => $this->kelas_model->get_all(),
			'active_nav' => 'guru'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
        $this->load->view('master/guru/guru-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function username_check($username)
	{
		$uuid = $this->input->post('uuid');

		// Hanya melihat data AKTIF; data guru yang sudah dihapus tidak lagi
		// menghalangi pemakaian username yang sama.
		if ($this->guru_model->is_username_dipakai_aktif($username, $uuid)) {
			$this->form_validation->set_message('username_check', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}

		return true;
	}


	/**
	 * Hapus beberapa data guru sekaligus (soft delete).
	 * Dipanggil dari tombol "Hapus Terpilih" pada halaman Data Guru.
	 */
	public function bulk_hapus()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('guru');
		}

		$uuids = $this->input->post('guru_uuids');

		if (!is_array($uuids) || empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada guru yang dipilih');
			redirect('guru');
		}

		// Bersihkan nilai kosong & duplikat
		$uuids = array_values(array_filter(array_unique($uuids), function ($u) {
			return is_string($u) && $u !== '';
		}));

		if (empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada guru yang dipilih');
			redirect('guru');
		}

		$deleted = $this->guru_model->delete_batch_by_uuid($uuids);

		if ($deleted > 0) {
			$this->session->set_flashdata('success_msg', $deleted . ' data guru berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data guru');
		}

		redirect('guru');
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

	/**
	 * Halaman import data guru dari Excel.
	 * Saat request POST, file Excel yang diupload langsung diproses.
	 */
	public function import_excel()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->_process_import_excel('guru/import');
		}

		$this->load->view('partials/header_tailwind', ['title' => 'Import Data Guru']);
		$this->load->view('partials/navbar', ['active_nav' => 'guru']);
		$this->load->view('master/guru/guru-import', array(
			'daftar_mapel' => $this->mapel_model->get_all(),
			'daftar_kelas' => $this->kelas_model->get_all(),
			'from_controller' => true
		));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Proses upload & import file Excel data guru.
	 *
	 * @param string $redirect URL tujuan setelah proses selesai
	 */
	private function _process_import_excel($redirect)
	{
		if (empty($_FILES['file_excel']['name'])) {
			$this->session->set_flashdata('error_msg', 'Pilih file Excel terlebih dahulu');
			redirect($redirect);
		}

		if (!is_dir('./uploads/')) {
			mkdir('./uploads/', 0777, TRUE);
		}

		$upload_config = array(
			'upload_path' => './uploads/',
			'allowed_types' => 'xlsx|xls',
			'max_size' => 5120,
			'encrypt_name' => TRUE
		);
		$this->load->library('upload', $upload_config);

		if (!$this->upload->do_upload('file_excel')) {
			$this->session->set_flashdata('error_msg', strip_tags($this->upload->display_errors()));
			redirect($redirect);
		}

		$upload_data = $this->upload->data();
		$file_path = './uploads/' . $upload_data['file_name'];

		try {
			$spreadsheet = IOFactory::load($file_path);
			$sheet = $spreadsheet->getSheetByName('Template Guru');
			if (!$sheet) {
				$sheet = $spreadsheet->getActiveSheet();
			}
			$rows = $sheet->toArray();

			// Buang baris header
			array_shift($rows);

			// Peta mata pelajaran & kelas: nama (dinormalisasi) => data
			$mapel_map = array();
			foreach ($this->mapel_model->get_all() as $mapel) {
				$mapel_map[$this->_normalize_key($mapel->nama)] = $mapel;
			}

			$kelas_map = array();
			foreach ($this->kelas_model->get_all() as $kelas) {
				$kelas_map[$this->_normalize_key($kelas->nama)] = $kelas;
			}

			// Data yang sudah ada dipakai untuk validasi keunikan username & NIP
			$existing_username = $this->guru_model->get_existing_username();
			$existing_nip = $this->guru_model->get_existing_nip();

			$inserted = 0;
			$errors = array();
			$no = 0;

			foreach ($rows as $row) {
				$no++;
				$baris = $no + 1; // baris asli pada file Excel (baris 1 = header)

				$nama = trim((string) ($row[0] ?? ''));
				$username = strtolower(trim((string) ($row[1] ?? '')));
				$nip = trim((string) ($row[2] ?? ''));
				$jenis_kelamin_raw = trim((string) ($row[3] ?? ''));
				$mapel_raw = trim((string) ($row[4] ?? ''));
				$kelas_raw = trim((string) ($row[5] ?? ''));

				// Lewati baris yang benar-benar kosong
				if ($nama === '' && $username === '' && $nip === '' && $jenis_kelamin_raw === '' && $mapel_raw === '' && $kelas_raw === '') {
					continue;
				}

				// --- Validasi nama ---
				if ($nama === '') {
					$errors[] = "Baris {$baris}: Nama Lengkap wajib diisi.";
					continue;
				}

				// --- Validasi jenis kelamin ---
				$jenis_kelamin = $this->_parse_jenis_kelamin($jenis_kelamin_raw);
				if (!$jenis_kelamin) {
					$errors[] = "Baris {$baris}: Jenis Kelamin '{$jenis_kelamin_raw}' tidak valid. Gunakan L (Laki-laki) atau P (Perempuan).";
					continue;
				}

				// --- Validasi mata pelajaran (boleh lebih dari satu, dipisah koma) ---
				if ($mapel_raw === '') {
					$errors[] = "Baris {$baris}: Mata Pelajaran wajib diisi.";
					continue;
				}

				$mapel_uuids = array();
				$mapel_tidak_ditemukan = array();
				foreach ($this->_split_list($mapel_raw) as $nama_mapel) {
					$key = $this->_normalize_key($nama_mapel);
					if (!isset($mapel_map[$key])) {
						$mapel_tidak_ditemukan[] = $nama_mapel;
						continue;
					}
					$mapel_uuids[$mapel_map[$key]->uuid] = TRUE;
				}

				if (!empty($mapel_tidak_ditemukan)) {
					$errors[] = "Baris {$baris}: Mata Pelajaran '" . implode(', ', $mapel_tidak_ditemukan) . "' tidak ditemukan. Pastikan namanya sama dengan data pada menu Data Mata Pelajaran.";
					continue;
				}

				if (empty($mapel_uuids)) {
					$errors[] = "Baris {$baris}: Mata Pelajaran tidak valid.";
					continue;
				}

				// --- Validasi kelas (opsional, boleh lebih dari satu) ---
				$kelas_uuids = array();
				if ($kelas_raw !== '') {
					$kelas_tidak_ditemukan = array();
					foreach ($this->_split_list($kelas_raw) as $nama_kelas) {
						$key = $this->_normalize_key($nama_kelas);
						if (!isset($kelas_map[$key])) {
							$kelas_tidak_ditemukan[] = $nama_kelas;
							continue;
						}
						$kelas_uuids[] = $kelas_map[$key]->uuid;
					}

					if (!empty($kelas_tidak_ditemukan)) {
						$errors[] = "Baris {$baris}: Kelas '" . implode(', ', $kelas_tidak_ditemukan) . "' tidak ditemukan. Pastikan nama kelas sama dengan data pada menu Data Kelas.";
						continue;
					}
				}

				// --- Validasi NIP (opsional, tidak boleh sama dengan NIP guru aktif lain) ---
				if ($nip !== '' && isset($existing_nip[$nip])) {
					$errors[] = "Baris {$baris}: NIP '{$nip}' sudah terdaftar pada guru aktif lain.";
					continue;
				}

				// --- Username: boleh dikosongkan, dibuat otomatis dari nama ---
				$username_dari_excel = ($username !== '');
				if (!$username_dari_excel) {
					$username = $this->_generate_username($nama);
				}

				if ($username === '' || !preg_match('/^[a-z][a-z0-9._-]*$/', $username)) {
					$errors[] = "Baris {$baris}: Username '{$username}' tidak valid. Gunakan huruf kecil, angka, titik, underscore, atau strip dan diawali huruf.";
					continue;
				}

				if (isset($existing_username[$username])) {
					if ($username_dari_excel) {
						$errors[] = "Baris {$baris}: Username '{$username}' sudah digunakan. Ganti username tersebut atau kosongkan kolom Username agar dibuat otomatis.";
						continue;
					}

					$username = $this->_find_unique_username($username, $existing_username);
					if ($username === NULL) {
						$errors[] = "Baris {$baris}: Gagal membuat username otomatis untuk '{$nama}'. Isi kolom Username secara manual.";
						continue;
					}
				}

				// --- Susun data mata pelajaran & kelas (format JSON yang sama dengan form) ---
				$mapel_kelas_data = array();
				foreach (array_keys($mapel_uuids) as $mapel_uuid) {
					$mapel_kelas_data[] = array(
						'mapel_uuid' => $mapel_uuid,
						'kelas_list' => array_values($kelas_uuids)
					);
				}

				// --- Simpan data guru ---
				$insert = $this->guru_model->insert_import(array(
					'nama' => $nama,
					'username' => $username,
					'nip' => $nip,
					'mapel_uuid' => json_encode($mapel_kelas_data),
					'jenis_kelamin' => $jenis_kelamin
				));

				if ($insert) {
					$inserted++;
					$existing_username[$username] = TRUE;
					if ($nip !== '') {
						$existing_nip[$nip] = TRUE;
					}
				} else {
					$errors[] = "Baris {$baris}: Gagal menyimpan data guru. " . $this->guru_model->last_db_error();
				}
			}

			// Hapus file upload
			@unlink($file_path);

			if ($inserted > 0) {
				$this->session->set_flashdata('success_msg', "{$inserted} data guru berhasil diimport dari Excel.");
			} elseif (empty($errors)) {
				$this->session->set_flashdata('error_msg', 'Tidak ada data yang diimport. Pastikan file Excel sudah diisi sesuai format.');
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

		redirect($redirect);
	}

	/**
	 * Normalisasi teks untuk pencocokan (huruf kecil tanpa spasi/tanda baca).
	 * Contoh: "Matematika Wajib" dan "matematika.wajib" dianggap sama.
	 *
	 * @param string $text
	 * @return string
	 */
	private function _normalize_key($text)
	{
		$text = strtolower(trim((string) $text));
		$text = preg_replace('/[^a-z0-9]+/', '', $text);

		return $text;
	}

	/**
	 * Pecah teks daftar (mata pelajaran / kelas) yang dipisah koma,
	 * titik koma, atau garis miring menjadi array tanpa duplikat.
	 *
	 * @param string $text
	 * @return array
	 */
	private function _split_list($text)
	{
		$items = preg_split('/[,;\\/|]+/', (string) $text);
		$hasil = array();

		foreach ($items as $item) {
			$item = trim($item);
			if ($item === '') {
				continue;
			}
			$key = strtolower($item);
			if (!isset($hasil[$key])) {
				$hasil[$key] = $item;
			}
		}

		return array_values($hasil);
	}

	/**
	 * Unduh format Excel (template) untuk import data guru.
	 * Sheet 1 berisi kolom yang harus diisi, sheet "Panduan" berisi petunjuk,
	 * serta sheet "Mapel" dan "Kelas" berisi daftar master data yang tersedia.
	 */
	public function download_template_guru()
	{
		$daftar_mapel = $this->mapel_model->get_all();
		$daftar_kelas = $this->kelas_model->get_all();
		$jumlah_mapel = count($daftar_mapel);
		$jumlah_kelas = count($daftar_kelas);

		$spreadsheet = new Spreadsheet();

		// ---------- Sheet 1: Template Guru ----------
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Guru');

		$headers = array('Nama Lengkap', 'Username', 'NIP', 'Jenis Kelamin', 'Mata Pelajaran', 'Kelas Diampu');
		$sheet->fromArray($headers, NULL, 'A1');

		$headerStyle = array(
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		);
		$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

		// Baris contoh (harus dihapus sebelum dipakai)
		$mapel_contoh = ($jumlah_mapel > 0) ? $daftar_mapel[0]->nama : 'Matematika';
		$kelas_contoh = ($jumlah_kelas > 0) ? $daftar_kelas[0]->nama : 'X-A';
		$contoh = array(
			array('Budi Santoso', 'budi.santoso', '198501012010011001', 'L', $mapel_contoh, $kelas_contoh),
			array('Siti Aminah', 'siti.aminah', '', 'P', $mapel_contoh, ''),
			array('Agus Wijaya', '', '199002022015031002', 'Laki-Laki', $mapel_contoh, $kelas_contoh)
		);
		$sheet->fromArray($contoh, NULL, 'A2');
		$sheet->getStyle('A2:F4')->getFont()->setItalic(TRUE);
		$sheet->getStyle('A2:F4')->getFont()->getColor()->setRGB('6B7280');

		// NIP dan Mata Pelajaran/Kelas diperlakukan sebagai teks agar tidak berubah format
		$sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
		$sheet->getStyle('E2:F1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

		// Dropdown Jenis Kelamin
		$jkValidation = $sheet->getDataValidation('D2');
		$jkValidation->setSqref('D2:D1000')
			->setType(DataValidation::TYPE_LIST)
			->setErrorStyle(DataValidation::STYLE_STOP)
			->setAllowBlank(true)
			->setShowDropDown(true)
			->setShowInputMessage(true)
			->setShowErrorMessage(true)
			->setErrorTitle('Jenis Kelamin tidak valid')
			->setError('Gunakan L (Laki-laki) atau P (Perempuan).')
			->setPromptTitle('Jenis Kelamin')
			->setPrompt('Isi dengan L (Laki-laki) atau P (Perempuan).')
			->setFormula1('"L,P"');

		// Lebar kolom & tampilan sheet
		$columnWidths = array('A' => 28, 'B' => 24, 'C' => 22, 'D' => 16, 'E' => 30, 'F' => 22);
		foreach ($columnWidths as $kolom => $lebar) {
			$sheet->getColumnDimension($kolom)->setWidth($lebar);
		}
		$sheet->getRowDimension(1)->setRowHeight(25);
		$sheet->freezePane('A2');

		// ---------- Sheet 2: Panduan ----------
		$panduanSheet = $spreadsheet->createSheet();
		$panduanSheet->setTitle('Panduan');

		$panduan = array(
			array('PANDUAN PENGISIAN FORMAT IMPORT DATA GURU'),
			array(''),
			array('Kolom', 'Wajib', 'Keterangan', 'Contoh'),
			array('Nama Lengkap', 'Ya', 'Nama lengkap guru.', 'Budi Santoso'),
			array('Username', 'Tidak', 'Username untuk login. Bila dikosongkan, sistem membuat username otomatis dari nama (huruf kecil, spasi menjadi titik). Bila sudah dipakai, sistem menambahkan angka.', 'budi.santoso'),
			array('NIP', 'Tidak', 'NIP/NUPTK guru (opsional). Bila diisi, tidak boleh sama dengan NIP guru aktif lainnya. Diisi sebagai teks agar angka nol di depan tidak hilang.', '198501012010011001'),
			array('Jenis Kelamin', 'Ya', 'Isi L (Laki-laki) atau P (Perempuan). Tersedia pilihan dropdown pada kolom ini.', 'L'),
			array('Mata Pelajaran', 'Ya', 'Nama mata pelajaran yang diampu. Bila lebih dari satu, pisahkan dengan koma (misal: Matematika, Koding). Nama harus sama dengan data master (lihat sheet "Mapel").', 'Koding'),
			array('Kelas Diampu', 'Tidak', 'Nama kelas yang diajar. Bila lebih dari satu, pisahkan dengan koma. Kelas yang diisi berlaku untuk semua mata pelajaran pada baris tersebut. Nama harus sama dengan data master (lihat sheet "Kelas").', 'X-A'),
			array(''),
			array('CATATAN:'),
			array('1. Hapus baris contoh (baris 2 sampai 4) sebelum mengisi data guru.'),
			array('2. Satu baris = satu guru.'),
			array('3. Jangan mengubah atau menghapus baris header (baris 1).'),
			array('4. Baris kosong akan dilewati saat proses import.'),
			array('5. Password awal guru hasil import adalah edu12345 (sama seperti tambah manual).'),
			array('6. File yang bisa diupload: .xlsx atau .xls, maksimal 5 MB.'),
			array('7. Guru yang gagal diimport akan dilaporkan setelah proses import selesai.')
		);
		$panduanSheet->fromArray($panduan, NULL, 'A1');
		$panduanSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$panduanSheet->getStyle('A3:D3')->getFont()->setBold(true);
		$panduanSheet->getStyle('A12')->getFont()->setBold(true);
		$panduanSheet->getColumnDimension('A')->setWidth(18);
		$panduanSheet->getColumnDimension('B')->setWidth(10);
		$panduanSheet->getColumnDimension('C')->setWidth(90);
		$panduanSheet->getColumnDimension('D')->setWidth(22);

		// ---------- Sheet 3: Mapel ----------
		$mapelSheet = $spreadsheet->createSheet();
		$mapelSheet->setTitle('Mapel');
		$mapelSheet->setCellValue('A1', 'Nama Mata Pelajaran');
		$mapelSheet->getStyle('A1')->getFont()->setBold(true);
		$mapelSheet->getColumnDimension('A')->setWidth(35);

		$mapelBaris = 1;
		foreach ($daftar_mapel as $mapel) {
			$mapelBaris++;
			$mapelSheet->setCellValueExplicit('A' . $mapelBaris, $mapel->nama, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		}

		// ---------- Sheet 4: Kelas ----------
		$kelasSheet = $spreadsheet->createSheet();
		$kelasSheet->setTitle('Kelas');
		$kelasSheet->setCellValue('A1', 'Nama Kelas');
		$kelasSheet->getStyle('A1')->getFont()->setBold(true);
		$kelasSheet->getColumnDimension('A')->setWidth(30);

		$kelasBaris = 1;
		foreach ($daftar_kelas as $kelas) {
			$kelasBaris++;
			$kelasSheet->setCellValueExplicit('A' . $kelasBaris, $kelas->nama, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		}

		$spreadsheet->setActiveSheetIndex(0);

		// ---------- Output file ----------
		$filename = 'format_import_guru.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	/**
	 * Ubah input jenis kelamin dari Excel menjadi kode angka.
	 *
	 * @param string $value
	 * @return int|null 1 = Laki-laki, 2 = Perempuan, NULL bila tidak valid
	 */
	private function _parse_jenis_kelamin($value)
	{
		$value = strtolower(trim((string) $value));

		if ($value === '') {
			return NULL;
		}

		$laki_laki = array('l', 'lk', 'laki-laki', 'laki laki', 'laki2', 'male', 'm', 'pria', '1');
		$perempuan = array('p', 'pr', 'perempuan', 'female', 'f', 'wanita', '2');

		if ($value === 'l' || $value === 'p') {
			return ($value === 'l') ? 1 : 2;
		}

		if (in_array($value, $laki_laki, TRUE)) {
			return 1;
		}

		if (in_array($value, $perempuan, TRUE)) {
			return 2;
		}

		return NULL;
	}

	/**
	 * Buat username otomatis dari nama lengkap guru.
	 * Contoh: "Budi Santoso" => "budi.santoso"
	 *
	 * @param string $nama
	 * @return string
	 */
	private function _generate_username($nama)
	{
		$username = strtolower(trim((string) $nama));
		$username = str_replace(array("'", "`"), '', $username);
		$username = preg_replace('/[^a-z0-9]+/', '.', $username);
		$username = trim($username, '.');

		if ($username !== '' && !preg_match('/^[a-z]/', $username)) {
			$username = 'g.' . $username;
		}

		return $username;
	}

	/**
	 * Cari username yang belum dipakai dengan menambahkan angka di belakangnya.
	 *
	 * @param string $username username dasar
	 * @param array $taken daftar username yang sudah dipakai
	 * @return string|null username unik atau NULL bila tidak ditemukan
	 */
	private function _find_unique_username($username, $taken)
	{
		$username = trim($username, '.');

		if ($username === '') {
			return NULL;
		}

		if (!isset($taken[$username])) {
			return $username;
		}

		for ($i = 2; $i <= 99; $i++) {
			$kandidat = $username . $i;
			if (!isset($taken[$kandidat])) {
				return $kandidat;
			}
		}

		return NULL;
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
		$this->load->view('master/guru/guru-jadwal', array_merge($data, ['from_controller' => true]));
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