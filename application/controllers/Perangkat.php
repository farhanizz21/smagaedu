<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perangkat extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('perangkat_model');
        $this->load->model('mapel_model');
        $this->load->model('guru_model');
    }

    public function index()
    {
        $user_role = user_role();
        $user_uuid = $this->session->userdata('uuid');

        $per_page_options = array(10, 25, 50, 100);
        $per_page = (int) $this->input->get('per_page');
        if (!in_array($per_page, $per_page_options, TRUE)) {
            $per_page = 10;
        }
        $page = max(1, (int) $this->input->get('page'));

        if (in_array($user_role, ['superadmin', 'admin'])) {
            // Admin/superadmin: lihat semua mapel
            $total_rows = $this->perangkat_model->count_all_mapel();
        } else {
            // Guru: lihat mapel yang diampu
            $total_rows = $this->perangkat_model->count_mapel_by_guru($user_uuid);
        }
        $total_pages = max(1, (int) ceil($total_rows / $per_page));
        $page = min($page, $total_pages);
        $offset = ($page - 1) * $per_page;

        if (in_array($user_role, ['superadmin', 'admin'])) {
            $mapel = $this->perangkat_model->get_all_mapel($per_page, $offset);
        } else {
            $mapel = $this->perangkat_model->get_mapel_by_guru($user_uuid, $per_page, $offset);
        }

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url'             => base_url('perangkat'),
            'total_rows'           => $total_rows,
            'per_page'             => $per_page,
            'use_page_numbers'     => TRUE,
            'page_query_string'    => TRUE,
            'query_string_segment' => 'page',
            'reuse_query_string'   => TRUE,
            'cur_page'             => $page,
            'num_links'            => 2,
            'first_link'           => '&laquo;',
            'prev_link'            => '&lsaquo;',
            'next_link'            => '&rsaquo;',
            'last_link'            => '&raquo;',
            'full_tag_open'        => '<nav aria-label="Navigasi halaman"><ul class="flex flex-wrap items-center justify-center gap-1 list-none p-0 m-0">',
            'full_tag_close'       => '</ul></nav>',
            'num_tag_open'         => '<li>',
            'num_tag_close'        => '</li>',
            'cur_tag_open'         => '<li><span class="min-w-[38px] h-9 px-3 inline-flex items-center justify-center rounded-lg text-sm font-semibold text-white bg-blue-600 border border-blue-600">',
            'cur_tag_close'        => '</span></li>',
            'first_tag_open'       => '<li>',
            'first_tag_close'      => '</li>',
            'prev_tag_open'        => '<li>',
            'prev_tag_close'       => '</li>',
            'next_tag_open'        => '<li>',
            'next_tag_close'       => '</li>',
            'last_tag_open'        => '<li>',
            'last_tag_close'       => '</li>',
            'attributes'           => array(
                'class' => 'min-w-[38px] h-9 px-3 inline-flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-colors',
            ),
        ));

        $data = array(
            'mapel' => $mapel,
            'is_admin' => is_admin_or_superadmin(),
            'active_nav' => 'perangkat',
            'per_page' => $per_page,
            'per_page_options' => $per_page_options,
            'total_rows' => $total_rows,
            'start_no' => $total_rows > 0 ? $offset + 1 : 0,
            'end_no' => min($offset + $per_page, $total_rows),
            'pagination_links' => $this->pagination->create_links(),
        );

        $this->load->view('partials/header_tailwind', ['title' => 'Perangkat Mata Pelajaran']);
        $this->load->view('partials/navbar', ['active_nav' => 'perangkat']);
        $this->load->view('perangkat/perangkat', array_merge($data, ['from_controller' => true]));
        $this->load->view('partials/footer_tailwind');
    }

    public function detail($mapel_uuid)
    {
        // Cek akses
        if (!$this->perangkat_model->can_access($mapel_uuid)) {
            show_error('Anda tidak memiliki akses ke mata pelajaran ini.', 403);
        }

        $mapel = $this->mapel_model->get_by_uuid($mapel_uuid, FALSE);
        if (empty($mapel)) {
            show_404();
        }

        $user_role = user_role();
        $user_uuid = $this->session->userdata('uuid');
        
        if (in_array($user_role, ['superadmin', 'admin'])) {
            // Admin/superadmin: lihat semua perangkat
            $perangkat = $this->perangkat_model->get_perangkat_by_mapel($mapel_uuid);
        } else {
            // Guru: lihat perangkat miliknya sendiri
            $perangkat = $this->perangkat_model->get_perangkat_by_mapel_and_guru($mapel_uuid, $user_uuid);
        }

        $guru_uuid = $this->session->userdata('uuid');
        $pengampu = $this->guru_model->get_mapel_pengampu($mapel_uuid, $guru_uuid);

        $data = array(
            'mapel' => $mapel,
            'perangkat' => $perangkat,
            'pengampu' => $pengampu,
            'is_admin' => is_admin_or_superadmin(),
            'active_nav' => 'perangkat',
            'jenis_file_options' => $this->perangkat_model->get_jenis_file_options(),
        );

        $this->load->view('partials/header_tailwind', ['title' => 'Perangkat - ' . $mapel->nama]);
        $this->load->view('partials/navbar', ['active_nav' => 'perangkat']);
        $this->load->view('perangkat/perangkat-detail', array_merge($data, ['from_controller' => true]));
        $this->load->view('partials/footer_tailwind');
    }

    public function upload($mapel_uuid, $jenis_file = NULL)
    {
        if ($jenis_file === NULL) {
            $jenis_file = $this->input->post('jenis_file');
        }

        // Validasi jenis file
        $jenis_file_options = $this->perangkat_model->get_jenis_file_options();
        if (!is_string($jenis_file) || !array_key_exists($jenis_file, $jenis_file_options)) {
            show_404();
        }

        // Cek akses
        if (!$this->perangkat_model->can_access($mapel_uuid)) {
            show_error('Anda tidak memiliki akses ke mata pelajaran ini.', 403);
        }

        $mapel = $this->mapel_model->get_by_uuid($mapel_uuid, FALSE);
        if (empty($mapel)) {
            show_404();
        }

        $this->load->library('upload');

        $config = array(
            'upload_path'   => FCPATH . 'uploads/perangkat/',
            'allowed_types' => 'pdf',
            'max_size'      => 10240, // 10MB
        );
        $this->upload->initialize($config);

        $nama_file = $this->input->post('nama_file');
        if (empty($nama_file)) {
            $this->session->set_flashdata('error_msg', 'Nama file harus diisi');
            redirect('perangkat/detail/' . $mapel_uuid);
        }

        if (!empty($_FILES['file_perangkat']['name'])) {
            if (!$this->upload->do_upload('file_perangkat')) {
                $this->session->set_flashdata('error_msg', 'Gagal mengupload file: ' . strip_tags($this->upload->display_errors()));
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            $uploaded_file = $this->upload->data();
            $file_path = $uploaded_file['full_path'];
            $mime = function_exists('finfo_open')
                ? (new finfo(FILEINFO_MIME_TYPE))->file($file_path)
                : FALSE;
            $header = file_get_contents($file_path, FALSE, NULL, 0, 1024);
            $is_pdf = strtolower(pathinfo($uploaded_file['file_name'], PATHINFO_EXTENSION)) === 'pdf'
                && $mime === 'application/pdf'
                && is_string($header)
                && strpos($header, '%PDF-') !== FALSE;

            if (!$is_pdf) {
                unlink($file_path);
                $this->session->set_flashdata('error_msg', 'File harus berupa dokumen PDF yang valid.');
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            $compression = $this->perangkat_model->compress_pdf_if_needed($file_path);
            if (!$compression['success']) {
                unlink($file_path);
                $this->session->set_flashdata('error_msg', 'PDF belum berhasil dikompres. Silakan kompres file secara manual menggunakan tautan di bawah, lalu unggah kembali.');
                $this->session->set_flashdata('pdf_compress_help', TRUE);
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            $uuid = $this->perangkat_model->insert($mapel_uuid, $jenis_file);
            if (!$uuid) {
                unlink($file_path);
                $this->session->set_flashdata('error_msg', 'Gagal menyimpan data perangkat.');
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            $file_name = $uploaded_file['file_name'];
            if (!$this->perangkat_model->update_file($uuid, $file_name)) {
                unlink($file_path);
                $this->perangkat_model->delete_by_uuid($uuid);
                $this->session->set_flashdata('error_msg', 'Gagal menyimpan file perangkat.');
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            $success_message = 'File ' . $jenis_file_options[$jenis_file] . ' berhasil diupload.';
            if ($compression['compressed']) {
                $success_message .= ' Ukuran PDF berhasil dikompres.';
            }
            $this->session->set_flashdata('success_msg', $success_message);
        } else {
            $this->session->set_flashdata('error_msg', 'File harus dipilih');
        }

        redirect('perangkat/detail/' . $mapel_uuid);
    }

    public function hapus($uuid)
    {
        $perangkat = $this->perangkat_model->get_by_uuid($uuid);
        if (empty($perangkat)) {
            show_404();
        }

        // Cek akses: superadmin/admin/pemilik
        $can_delete = is_superadmin()
            || (has_role('admin') && admin_can_access_creator($perangkat->guru_uuid))
            || ($perangkat->guru_uuid == $this->session->userdata('uuid'));
        
        if (!$can_delete) {
            show_error('Anda tidak memiliki akses untuk menghapus file ini.', 403);
        }

        // Hapus file fisik
        $file_path = FCPATH . 'uploads/perangkat/' . $perangkat->file;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $result = $this->perangkat_model->delete_by_uuid($uuid);
        if ($result) {
            $this->session->set_flashdata('success_msg', 'File berhasil dihapus');
        } else {
            $this->session->set_flashdata('error_msg', 'Gagal menghapus file');
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function download($uuid)
    {
        $perangkat = $this->perangkat_model->get_by_uuid($uuid);
        if (empty($perangkat)) {
            show_404();
        }

        $file_path = FCPATH . 'uploads/perangkat/' . $perangkat->file;
        if (!file_exists($file_path)) {
            show_error('File tidak ditemukan.', 404);
        }

        $this->load->helper('download');
        force_download($file_path, NULL);
    }

    public function lihat($uuid)
    {
        $perangkat = $this->perangkat_model->get_by_uuid($uuid);
        if (empty($perangkat)) {
            show_404();
        }

        $file_name = basename($perangkat->file);
        $file_path = FCPATH . 'uploads/perangkat/' . $file_name;
        if (strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) !== 'pdf' || !is_file($file_path)) {
            show_error('File PDF tidak ditemukan.', 404);
        }

        $safe_file_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $file_name);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $safe_file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        readfile($file_path);
    }
}