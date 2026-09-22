<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Siswa extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('siswa_model');
		$this->load->model('kelas_model');
		$this->require_admin_or_superadmin(); // Superadmin dan admin bisa akses
	}

	public function index()
	{
		$siswa = $this->siswa_model->get_all();

		$data = array(
			'siswa' => $siswa,
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Data Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('master/siswa/siswa', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

    public function tambah()
	{
        $rules = $this->siswa_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->siswa_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data Siswa berhasil di simpan');
				redirect('siswa');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Siswa gagal di simpan');
				redirect('siswa');
			}
		}

		$data = array(
			'daftar_kelas' => $this->kelas_model->get_all(),
			'active_nav' => 'siswa'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('master/siswa/siswa-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		$rules = [
			[
				'field' => 'namaLengkap',
				'label' => 'Nama Lengkap',
				'rules' => 'required'
			],[
				'field' => 'nis',
				'label' => 'Nomor Induk Siswa',
				'rules' => 'required|regex_match[/^[0-9]{10}$/]|callback_nis_check'
			],[
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[a-z]/]|callback_username_check'
			],[
				'field' => 'jenisKelamin',
				'label' => 'Jenis Kelamin',
				'rules' => 'required'
			],[
				'field' => 'tanggal_lahir',
				'label' => 'Tanggal Lahir',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->siswa_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Siswa berhasil di Update');
				redirect('siswa');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Siswa gagal di Update');
				redirect('siswa');
			}
		}

		$data = array(
			'siswa' => $this->siswa_model->get_by_uuid($uuid),
			'daftar_kelas' => $this->kelas_model->get_all(),
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
        $this->load->view('master/siswa/siswa-edit', $data);
		$this->load->view('partials/footer_tailwind');
	}

	public function username_check($username)
	{
		$uuid = $this->input->post('uuid');

		// Data siswa aktif lain yang memakai username ini
		if ($this->siswa_model->is_username_dipakai_aktif($username, $uuid)) {
			$this->form_validation->set_message('username_check', 'Username sudah digunakan oleh pengguna lain.');
			return false;
		}

		return true;
	}

	public function nis_check($nis)
	{
		$uuid = $this->input->post('uuid');

		// Data siswa aktif lain yang memakai NIS ini (data yang sudah dihapus tidak lagi menghitung)
		if ($this->siswa_model->is_nis_dipakai_aktif($nis, $uuid)) {
			$this->form_validation->set_message('nis_check', 'NIS sudah digunakan oleh pengguna lain.');
			return false;
		}

		return true;
	}

	public function hapus($uuid){
		{
			$result = $this->siswa_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data siswa berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data siswa');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
				/**
	 * Hapus beberapa data siswa sekaligus (soft delete).
	 * Diinpulkan dari tombol "Hapus Terpilih" pada halaman Data Siswa.
	 */
	public function bulk_hapus()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('siswa');
		}

		$uuids = $this->input->post('siswa_uuids');

		if (!is_array($uuids) || empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada siswa yang dipilih');
			redirect('siswa');
		}

		// Bersihkan nilai kosong & duplikat
		$uuids = array_values(array_filter(array_unique($uuids), function ($u) {
			return is_string($u) && $u !== '';
		}));

		if (empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada siswa yang dipilih');
			redirect('siswa');
		}

		$deleted = $this->siswa_model->delete_batch_by_uuid($uuids);

		if ($deleted > 0) {
			$this->session->set_flashdata('success_msg', $deleted . ' data siswa berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data siswa');
		}

		redirect('siswa');
	}

	/**
	 * Halaman import data siswa dari Excel.
	 * Saat request POST, file Excel yang diupload langsung diproses.
	 */
	public function import_excel()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->_process_import_excel('siswa/import');
		}

		$this->load->view('partials/header_tailwind', ['title' => 'Import Data Siswa']);
		$this->load->view('partials/navbar', ['active_nav' => 'siswa']);
		$this->load->view('master/siswa/siswa-import', array(
			'daftar_kelas' => $this->kelas_model->get_all(),
			'from_controller' => true
		));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Proses upload & import file Excel data siswa.
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
			$sheet = $spreadsheet->getSheetByName('Template Siswa');
			if (!$sheet) {
				$sheet = $spreadsheet->getActiveSheet();
			}
			$rows = $sheet->toArray();

			// Buang baris header
			array_shift($rows);

			// Peta kelas: nama kelas (dinormalisasi) => data kelas
			$kelas_map = array();
			foreach ($this->kelas_model->get_all() as $kelas) {
				$kelas_map[$this->_normalize_key($kelas->nama)] = $kelas;
			}

			// Data yang sudah ada dipakai untuk validasi keunikan NIS & username
			$existing_username = $this->siswa_model->get_existing_username();
			$existing_nis = $this->siswa_model->get_existing_nis();

			$inserted = 0;
			$errors = array();
			$no = 0;

			foreach ($rows as $row) {
				$no++;
				$baris = $no + 1; // baris asli pada file Excel (baris 1 = header)

				$nis = trim((string) ($row[0] ?? ''));
				$nama = trim((string) ($row[1] ?? ''));
				$username = strtolower(trim((string) ($row[2] ?? '')));
				$tgl_lahir_raw = $row[3] ?? '';
				$jenis_kelamin_raw = trim((string) ($row[4] ?? ''));
				$kelas_raw = trim((string) ($row[5] ?? ''));

				// Lewati baris yang benar-benar kosong
				if ($nis === '' && $nama === '' && $username === '' && trim((string) $tgl_lahir_raw) === '' && $jenis_kelamin_raw === '' && $kelas_raw === '') {
					continue;
				}

				// --- Validasi NIS ---
				// Panjang NIS harus sama dengan validasi form Data Siswa (Siswa_model::rules()).
				if ($nis === '') {
					$errors[] = "Baris {$baris}: NIS wajib diisi.";
					continue;
				}
				if (!preg_match('/^[0-9]{10}$/', $nis)) {
					$errors[] = "Baris {$baris}: NIS '{$nis}' tidak valid. NIS harus 10 digit angka.";
					continue;
				}
				if (isset($existing_nis[(int) $nis])) {
					$errors[] = "Baris {$baris}: NIS '{$nis}' sudah terdaftar (termasuk data siswa yang sudah dihapus).";
					continue;
				}

				// --- Validasi nama ---
				if ($nama === '') {
					$errors[] = "Baris {$baris}: Nama Lengkap wajib diisi.";
					continue;
				}

				// --- Validasi tanggal lahir ---
				$tgl_lahir = $this->_parse_tanggal($tgl_lahir_raw);
				if (!$tgl_lahir) {
					$errors[] = "Baris {$baris}: Tanggal Lahir '" . trim((string) $tgl_lahir_raw) . "' tidak valid. Gunakan format YYYY-MM-DD atau DD/MM/YYYY.";
					continue;
				}

				// --- Validasi jenis kelamin ---
				$jenis_kelamin = $this->_parse_jenis_kelamin($jenis_kelamin_raw);
				if (!$jenis_kelamin) {
					$errors[] = "Baris {$baris}: Jenis Kelamin '{$jenis_kelamin_raw}' tidak valid. Gunakan L (Laki-laki) atau P (Perempuan).";
					continue;
				}

				// --- Validasi kelas ---
				if ($kelas_raw === '') {
					$errors[] = "Baris {$baris}: Kelas wajib diisi.";
					continue;
				}
				$kelas_key = $this->_normalize_key($kelas_raw);
				if (!isset($kelas_map[$kelas_key])) {
					$errors[] = "Baris {$baris}: Kelas '{$kelas_raw}' tidak ditemukan. Pastikan nama kelas sama dengan data pada menu Data Kelas.";
					continue;
				}
				$kelas_uuid = $kelas_map[$kelas_key]->uuid;

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

				// --- Simpan data siswa ---
				$insert = $this->siswa_model->insert_import(array(
					'nis' => $nis,
					'nama' => $nama,
					'username' => $username,
					'tgl_lahir' => $tgl_lahir,
					'jenis_kelamin' => $jenis_kelamin,
					'kelas_uuid' => $kelas_uuid
				));

				if ($insert) {
					$inserted++;
					$existing_nis[(int) $nis] = TRUE;
					$existing_username[$username] = TRUE;
				} else {
					$errors[] = "Baris {$baris}: Gagal menyimpan data siswa. " . $this->siswa_model->last_db_error();
				}
			}

			// Hapus file upload
			@unlink($file_path);

			if ($inserted > 0) {
				$this->session->set_flashdata('success_msg', "{$inserted} data siswa berhasil diimport dari Excel.");
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
	 * Contoh: "XII IPA 1" dan "XII.IPA.1" dianggap sama.
	 *
	 * @param string $text
	 * @return string
	 */
	private function _normalize_key($text)
	{
		return preg_replace('/[^a-z0-9]/', '', strtolower((string) $text));
	}

	/**
	 * Ubah nilai kolom Tanggal Lahir dari Excel menjadi format Y-m-d.
	 * Mendukung tanggal Excel (angka serial), YYYY-MM-DD, DD/MM/YYYY, DD-MM-YYYY,
	 * dan DD.MM.YYYY. Tanggal di luar rentang 1950 s/d hari ini dianggap tidak valid.
	 *
	 * @param mixed $value
	 * @return string|null format Y-m-d atau NULL bila tidak valid
	 */
	private function _parse_tanggal($value)
	{
		if ($value === NULL || trim((string) $value) === '') {
			return NULL;
		}

		$hasil = NULL;

		if (is_numeric($value)) {
			// Tanggal yang tersimpan sebagai angka serial Excel
			try {
				$hasil = ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
			} catch (\Exception $e) {
				$hasil = NULL;
			}
		} else {
			$nilai = trim((string) $value);

			foreach (array('Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y') as $format) {
				$tanggal = \DateTime::createFromFormat($format, $nilai);
				if ($tanggal && $tanggal->format($format) === $nilai) {
					$hasil = $tanggal->format('Y-m-d');
					break;
				}
			}

			if ($hasil === NULL) {
				$timestamp = strtotime($nilai);
				$hasil = ($timestamp !== FALSE) ? date('Y-m-d', $timestamp) : NULL;
			}
		}

		if ($hasil === NULL || $hasil < '1950-01-01' || $hasil > date('Y-m-d')) {
			return NULL;
		}

		return $hasil;
	}

	/**
	 * Ubah nilai kolom Jenis Kelamin menjadi kode yang dipakai aplikasi
	 * (1 = Laki-laki, 2 = Perempuan).
	 *
	 * @param mixed $value
	 * @return int|null 1, 2, atau NULL bila tidak dikenali
	 */
	private function _parse_jenis_kelamin($value)
	{
		$peta = array(
			'l' => 1, 'lk' => 1, 'laki' => 1, 'laki-laki' => 1, 'laki laki' => 1, 'pria' => 1, 'male' => 1, 'm' => 1, '1' => 1,
			'p' => 2, 'pr' => 2, 'perempuan' => 2, 'wanita' => 2, 'female' => 2, 'f' => 2, '2' => 2
		);

		$nilai = strtolower(trim((string) $value));

		return isset($peta[$nilai]) ? $peta[$nilai] : NULL;
	}

	/**
	 * Buat username otomatis dari nama siswa (sama seperti bantuan pengisian
	 * username pada form Tambah Siswa: spasi diganti titik).
	 *
	 * @param string $nama
	 * @return string
	 */
	private function _generate_username($nama)
	{
		$username = strtolower(trim((string) $nama));
		$username = preg_replace('/[^a-z0-9]+/', '.', $username);
		$username = trim($username, '.');

		if ($username !== '' && !preg_match('/^[a-z]/', $username)) {
			$username = 's.' . $username;
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

	/**
	 * Unduh format Excel (template) untuk import data siswa.
	 * Sheet 1 berisi kolom yang harus diisi, sheet "Panduan" berisi petunjuk,
	 * dan sheet "Kelas" berisi daftar kelas yang tersedia di master data.
	 */
	public function download_template_siswa()
	{
		$daftar_kelas = $this->kelas_model->get_all();
		$jumlah_kelas = count($daftar_kelas);

		$spreadsheet = new Spreadsheet();

		// ---------- Sheet 1: Template Siswa ----------
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Siswa');

		$headers = array('NIS', 'Nama Lengkap', 'Username', 'Tanggal Lahir', 'Jenis Kelamin', 'Kelas');
		$sheet->fromArray($headers, NULL, 'A1');

		$headerStyle = array(
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		);
		$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

		// Baris contoh (harus dihapus sebelum dipakai)
		$kelas_contoh = ($jumlah_kelas > 0) ? $daftar_kelas[0]->nama : 'XII IPA 1';
		$contoh = array(
			array('2024000001', 'Budi Santoso', 'budi.santoso', '2008-05-03', 'L', $kelas_contoh),
			array('2024000002', 'Siti Aminah', 'siti.aminah', '03/07/2008', 'P', $kelas_contoh),
			array('2024000003', 'Agus Wijaya', '', '15-09-2008', 'Laki-Laki', $kelas_contoh)
		);
		$sheet->fromArray($contoh, NULL, 'A2');
		$sheet->getStyle('A2:F4')->getFont()->setItalic(true);
		$sheet->getStyle('A2:F4')->getFont()->getColor()->setRGB('6B7280');

		// NIS dan Tanggal Lahir diperlakukan sebagai teks agar tidak berubah format
		$sheet->getStyle('A2:A1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
		$sheet->getStyle('D2:D1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

		// Dropdown Jenis Kelamin
		$jkValidation = $sheet->getDataValidation('E2');
		$jkValidation->setSqref('E2:E1000')
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
		$columnWidths = array('A' => 16, 'B' => 30, 'C' => 25, 'D' => 18, 'E' => 16, 'F' => 22);
		foreach ($columnWidths as $kolom => $lebar) {
			$sheet->getColumnDimension($kolom)->setWidth($lebar);
		}
		$sheet->getRowDimension(1)->setRowHeight(25);
		$sheet->freezePane('A2');

		// ---------- Sheet 2: Panduan ----------
		$panduanSheet = $spreadsheet->createSheet();
		$panduanSheet->setTitle('Panduan');

		$panduan = array(
			array('PANDUAN PENGISIAN FORMAT IMPORT DATA SISWA'),
			array(''),
			array('Kolom', 'Wajib', 'Keterangan', 'Contoh'),
			array('NIS', 'Ya', 'Nomor Induk Siswa, wajib 10 digit angka dan belum terdaftar.', '2024000001'),
			array('Nama Lengkap', 'Ya', 'Nama lengkap siswa.', 'Budi Santoso'),
			array('Username', 'Tidak', 'Username untuk login. Bila dikosongkan, sistem membuat username otomatis dari nama (huruf kecil, spasi menjadi titik). Bila sudah dipakai, sistem menambahkan angka.', 'budi.santoso'),
			array('Tanggal Lahir', 'Ya', 'Format YYYY-MM-DD atau DD/MM/YYYY. Boleh juga memakai format tanggal Excel.', '2008-05-03'),
			array('Jenis Kelamin', 'Ya', 'Isi L (Laki-laki) atau P (Perempuan). Tersedia pilihan dropdown pada kolom ini.', 'L'),
			array('Kelas', 'Ya', 'Nama kelas harus sama dengan data master kelas (lihat sheet "Kelas"). Tersedia pilihan dropdown pada kolom ini.', 'XII IPA 1'),
			array(''),
			array('CATATAN:'),
			array('1. Hapus baris contoh (baris 2 sampai 4) sebelum mengisi data siswa.'),
			array('2. Satu baris = satu siswa.'),
			array('3. Jangan mengubah atau menghapus baris header (baris 1).'),
			array('4. Baris kosong akan dilewati saat proses import.'),
			array('5. Password awal siswa hasil import adalah edu12345 (sama seperti tambah manual).'),
			array('6. File yang bisa diupload: .xlsx atau .xls, maksimal 5 MB.'),
			array('7. Siswa yang gagal diimport akan dilaporkan setelah proses import selesai.')
		);
		$panduanSheet->fromArray($panduan, NULL, 'A1');
		$panduanSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$panduanSheet->getStyle('A3:D3')->getFont()->setBold(true);
		$panduanSheet->getStyle('A11')->getFont()->setBold(true);
		$panduanSheet->getColumnDimension('A')->setWidth(18);
		$panduanSheet->getColumnDimension('B')->setWidth(10);
		$panduanSheet->getColumnDimension('C')->setWidth(90);
		$panduanSheet->getColumnDimension('D')->setWidth(20);

		// ---------- Sheet 3: Daftar Kelas ----------
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

		// Dropdown Kelas (mengambil daftar dari sheet "Kelas")
		if ($jumlah_kelas > 0) {
			$kelasValidation = $sheet->getDataValidation('F2');
			$kelasValidation->setSqref('F2:F1000')
				->setType(DataValidation::TYPE_LIST)
				->setErrorStyle(DataValidation::STYLE_STOP)
				->setAllowBlank(true)
				->setShowDropDown(true)
				->setShowInputMessage(true)
				->setShowErrorMessage(true)
				->setErrorTitle('Kelas tidak valid')
				->setError('Pilih kelas dari daftar. Bila kelas belum ada, tambahkan dulu pada menu Data Kelas.')
				->setPromptTitle('Kelas')
				->setPrompt('Pilih kelas dari daftar.')
				->setFormula1('Kelas!$A$2:$A$' . $kelasBaris);
		}

		$spreadsheet->setActiveSheetIndex(0);

		// ---------- Output file ----------
		$filename = 'format_import_siswa.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}
}
