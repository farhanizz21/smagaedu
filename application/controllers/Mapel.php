<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Mapel extends MY_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('mapel_model');
		$this->load->model('guru_model');
		// Superadmin dan admin bisa akses semua data
		// Guru hanya bisa akses data yang berelasi dengan dirinya
		if (!has_role(['admin', 'superadmin', 'guru'])) {
			show_error('Anda tidak memiliki akses ke halaman ini.', 403);
		}
	}

	public function index()
	{
		$user_uuid = $this->session->userdata('uuid');
		$user_role = $this->session->userdata('role');
		
		// Jika guru, tampilkan data yang dibuat sendiri + data yang diampu
		if ($user_role === 'guru') {
			$guru = $this->guru_model->get_by_uuid($user_uuid);
			$assigned_uuids = [];
			if ($guru && !empty($guru->mapel_uuid)) {
				$assigned_uuids = json_decode($guru->mapel_uuid, true);
			}
			$mapel = $this->mapel_model->get_all_by_guru_relation($user_uuid, $assigned_uuids);
		} else {
			$mapel = $this->mapel_model->get_all();
		}
		
		$data = array(
			'mapel' => $mapel,
			'active_nav' => 'mapel'
		);
		
        $this->load->view('partials/header_tailwind', ['title' => 'Data Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('master/mapel/mapel', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
    
    public function tambah()
	{
        $rules = $this->mapel_model->rules();
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$insert = $this->mapel_model->insert();
			if ($insert) {
				$this->session->set_flashdata('success_msg', 'Data mata pelajaran berhasil di simpan');
			}else {
				$this->session->set_flashdata('error_msg', 'Data mata pelajaran gagal di simpan');
			}
			redirect('mapel');
		}

		$data = array(
			'active_nav' => 'mapel'
		);
        
        $this->load->view('partials/header_tailwind', ['title' => 'Tambah Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('master/mapel/mapel-tambah', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function edit($uuid){
		// Cek kepemilikan/relasi data untuk guru
		$mapel = $this->mapel_model->get_by_uuid($uuid);
		if (!$mapel) {
			show_error('Data mata pelajaran tidak ditemukan.', 404);
		}
		
		$user_role = $this->session->userdata('role');
		$user_uuid = $this->session->userdata('uuid');
		if ($user_role === 'guru') {
			$guru = $this->guru_model->get_by_uuid($user_uuid);
			$assigned_uuids = [];
			if ($guru && !empty($guru->mapel_uuid)) {
				$assigned_uuids = json_decode($guru->mapel_uuid, true);
			}
			if ($mapel->created_by !== $user_uuid && !in_array($mapel->uuid, $assigned_uuids)) {
				show_error('Anda tidak memiliki akses untuk mengedit data ini.', 403);
			}
		}
		
		$rules = [
			[
				'field' => 'namaMapel',
				'label' => 'Nama Mata Pelajaran',
				'rules' => 'required'
			]
		];
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) {
			$update = $this->mapel_model->update($uuid);
			if ($update) {
				$this->session->set_flashdata('success_msg', 'Data Mata Pelajaran berhasil di Update');
				redirect('mapel');
			}else {
				$this->session->set_flashdata('error_msg', 'Data Mata Pelajaran gagal di Update');
				redirect('mapel');
			}
		}

		$data = array(
			'mapel' => $mapel,
			'active_nav' => 'mapel'
		);

		$this->load->view('partials/header_tailwind', ['title' => 'Edit Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
        $this->load->view('master/mapel/mapel-edit', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}

	public function hapus($uuid){
		{
			// Cek kepemilikan/relasi data untuk guru
			$mapel = $this->mapel_model->get_by_uuid($uuid);
			if (!$mapel) {
				show_error('Data mata pelajaran tidak ditemukan.', 404);
			}
			
			$user_role = $this->session->userdata('role');
			$user_uuid = $this->session->userdata('uuid');
			if ($user_role === 'guru') {
				$guru = $this->guru_model->get_by_uuid($user_uuid);
				$assigned_uuids = [];
				if ($guru && !empty($guru->mapel_uuid)) {
					$assigned_uuids = json_decode($guru->mapel_uuid, true);
				}
				if ($mapel->created_by !== $user_uuid && !in_array($mapel->uuid, $assigned_uuids)) {
					show_error('Anda tidak memiliki akses untuk menghapus data ini.', 403);
				}
			}			
			$result = $this->mapel_model->delete_by_uuid($uuid);
			if ($result) {
				$this->session->set_flashdata('success_msg', 'Data mata pelajaran berhasil dihapus');
			} else {
				$this->session->set_flashdata('error_msg', 'Gagal menghapus data mata pelajaran');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	/**
	 * Hapus beberapa data mata pelajaran sekaligus (soft delete).
	 * Dipanggil dari tombol "Hapus Terpilih" pada halaman Data Mata Pelajaran.
	 * Hanya admin/superadmin (guru hanya boleh menghapus satu per satu).
	 */
	public function bulk_hapus()
	{
		$this->require_admin_or_superadmin();

		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('mapel');
		}

		$uuids = $this->input->post('mapel_uuids');

		if (!is_array($uuids) || empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada mata pelajaran yang dipilih');
			redirect('mapel');
		}

		// Bersihkan nilai kosong & duplikat
		$uuids = array_values(array_filter(array_unique($uuids), function ($u) {
			return is_string($u) && $u !== '';
		}));

		if (empty($uuids)) {
			$this->session->set_flashdata('error_msg', 'Tidak ada mata pelajaran yang dipilih');
			redirect('mapel');
		}

		$deleted = $this->mapel_model->delete_batch_by_uuid($uuids);

		if ($deleted > 0) {
			$this->session->set_flashdata('success_msg', $deleted . ' data mata pelajaran berhasil dihapus');
		} else {
			$this->session->set_flashdata('error_msg', 'Gagal menghapus data mata pelajaran');
		}

		redirect('mapel');
	}

	/**
	 * Halaman import data mata pelajaran dari Excel.
	 * Saat request POST, file Excel yang diupload langsung diproses.
	 */
	public function import_excel()
	{
		$this->require_admin_or_superadmin();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->_process_import_excel('mapel/import');
		}

		$this->load->view('partials/header_tailwind', ['title' => 'Import Data Mata Pelajaran']);
		$this->load->view('partials/navbar', ['active_nav' => 'mapel']);
		$this->load->view('master/mapel/mapel-import', array(
			'from_controller' => true
		));
		$this->load->view('partials/footer_tailwind');
	}

	/**
	 * Unduh format Excel (template) untuk import data mata pelajaran.
	 * Sheet 1 berisi kolom yang harus diisi, sheet "Panduan" berisi petunjuk.
	 */
	public function download_template_mapel()
	{
		$this->require_admin_or_superadmin();

		$spreadsheet = new Spreadsheet();

		// ---------- Sheet 1: Template Mapel ----------
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Template Mapel');

		$sheet->fromArray(array('Nama Mata Pelajaran'), NULL, 'A1');

		$headerStyle = array(
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		);
		$sheet->getStyle('A1')->applyFromArray($headerStyle);

		// Baris contoh (harus dihapus sebelum dipakai)
		$contoh = array(
			array('Matematika'),
			array('Bahasa Indonesia'),
			array('Koding')
		);
		$sheet->fromArray($contoh, NULL, 'A2');
		$sheet->getStyle('A2:A4')->getFont()->setItalic(TRUE);
		$sheet->getStyle('A2:A4')->getFont()->getColor()->setRGB('6B7280');

		$sheet->getStyle('A2:A1000')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
		$sheet->getColumnDimension('A')->setWidth(35);
		$sheet->getRowDimension(1)->setRowHeight(25);
		$sheet->freezePane('A2');

		// ---------- Sheet 2: Panduan ----------
		$panduanSheet = $spreadsheet->createSheet();
		$panduanSheet->setTitle('Panduan');

		$panduan = array(
			array('PANDUAN PENGISIAN FORMAT IMPORT DATA MATA PELAJARAN'),
			array(''),
			array('Kolom', 'Wajib', 'Keterangan', 'Contoh'),
			array('Nama Mata Pelajaran', 'Ya', 'Nama mata pelajaran. Satu baris = satu mata pelajaran. Nama tidak boleh sama dengan data yang sudah ada maupun antar baris dalam file.', 'Koding'),
			array(''),
			array('CATATAN:'),
			array('1. Hapus baris contoh (baris 2 sampai 4) sebelum mengisi data.'),
			array('2. Satu baris = satu mata pelajaran.'),
			array('3. Jangan mengubah atau menghapus baris header (baris 1).'),
			array('4. Baris kosong akan dilewati saat proses import.'),
			array('5. Materi/bab tidak diisi dari file ini; kelola lewat menu masing-masing setelah mapel dibuat.'),
			array('6. File yang bisa diupload: .xlsx atau .xls, maksimal 5 MB.'),
			array('7. Mata pelajaran yang gagal diimport akan dilaporkan setelah proses import selesai.')
		);
		$panduanSheet->fromArray($panduan, NULL, 'A1');
		$panduanSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$panduanSheet->getStyle('A3:D3')->getFont()->setBold(true);
		$panduanSheet->getStyle('A6')->getFont()->setBold(true);
		$panduanSheet->getColumnDimension('A')->setWidth(22);
		$panduanSheet->getColumnDimension('B')->setWidth(10);
		$panduanSheet->getColumnDimension('C')->setWidth(95);
		$panduanSheet->getColumnDimension('D')->setWidth(22);

		$spreadsheet->setActiveSheetIndex(0);

		// ---------- Output file ----------
		$filename = 'format_import_mapel.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	/**
	 * Proses upload & import file Excel data mata pelajaran.
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
			$sheet = $spreadsheet->getSheetByName('Template Mapel');
			if (!$sheet) {
				$sheet = $spreadsheet->getActiveSheet();
			}
			$rows = $sheet->toArray();

			// Buang baris header
			array_shift($rows);

			// Nama yang sudah dipakai data aktif (untuk validasi keunikan)
			$existing_nama = $this->mapel_model->get_existing_nama();

			$inserted = 0;
			$errors = array();
			$no = 0;

			foreach ($rows as $row) {
				$no++;
				$baris = $no + 1; // baris asli pada file Excel (baris 1 = header)

				$nama = trim((string) ($row[0] ?? ''));

				// Lewati baris kosong
				if ($nama === '') {
					continue;
				}

				// --- Validasi panjang nama (kolom database varchar(100)) ---
				if (mb_strlen($nama) > 100) {
					$errors[] = "Baris {$baris}: Nama Mata Pelajaran terlalu panjang (maksimal 100 karakter).";
					continue;
				}

				// --- Validasi keunikan (data aktif & antar baris dalam file) ---
				$nama_key = strtolower($nama);
				if (isset($existing_nama[$nama_key])) {
					$errors[] = "Baris {$baris}: Mata Pelajaran '{$nama}' sudah ada.";
					continue;
				}

				// --- Simpan data ---
				$insert = $this->mapel_model->insert_import($nama, $this->session->userdata('uuid'));

				if ($insert) {
					$inserted++;
					$existing_nama[$nama_key] = TRUE;
				} else {
					$errors[] = "Baris {$baris}: Gagal menyimpan data mata pelajaran. " . $this->mapel_model->last_db_error();
				}
			}

			// Hapus file upload
			@unlink($file_path);

			if ($inserted > 0) {
				$this->session->set_flashdata('success_msg', "{$inserted} data mata pelajaran berhasil diimport dari Excel.");
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
    
}