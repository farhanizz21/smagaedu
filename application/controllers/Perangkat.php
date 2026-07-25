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
        $user_role = $this->session->userdata('role');
        $user_uuid = $this->session->userdata('uuid');
        
        if (in_array($user_role, ['superadmin', 'admin'])) {
            // Admin/superadmin: lihat semua mapel
            $mapel = $this->perangkat_model->get_all_mapel();
        } else {
            // Guru: lihat mapel yang diampu
            $mapel = $this->perangkat_model->get_mapel_by_guru($user_uuid);
        }

        $data = array(
            'mapel' => $mapel,
            'is_admin' => is_admin_or_superadmin(),
            'active_nav' => 'perangkat'
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

        $mapel = $this->mapel_model->get_by_uuid($mapel_uuid);
        if (empty($mapel)) {
            show_404();
        }

        $user_role = $this->session->userdata('role');
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
            'active_nav' => 'perangkat'
        );

        $this->load->view('partials/header_tailwind', ['title' => 'Perangkat - ' . $mapel->nama]);
        $this->load->view('partials/navbar', ['active_nav' => 'perangkat']);
        $this->load->view('perangkat/perangkat-detail', array_merge($data, ['from_controller' => true]));
        $this->load->view('partials/footer_tailwind');
    }

    public function upload($mapel_uuid, $jenis_file)
    {
        // Validasi jenis file
        if (!in_array($jenis_file, ['modul', 'atp'])) {
            show_404();
        }

        // Cek akses
        if (!$this->perangkat_model->can_access($mapel_uuid)) {
            show_error('Anda tidak memiliki akses ke mata pelajaran ini.', 403);
        }

        $mapel = $this->mapel_model->get_by_uuid($mapel_uuid);
        if (empty($mapel)) {
            show_404();
        }

        $this->load->library('upload');

        $config = array(
            'upload_path'   => FCPATH . 'uploads/perangkat/',
            'allowed_types' => "pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar",
            'max_size'      => 10240, // 10MB
        );
        $this->upload->initialize($config);

        $nama_file = $this->input->post('nama_file');
        if (empty($nama_file)) {
            $this->session->set_flashdata('error_msg', 'Nama file harus diisi');
            redirect('perangkat/detail/' . $mapel_uuid);
        }

        if (!empty($_FILES['file_perangkat']['name'])) {
            // Insert dulu untuk dapat uuid
            $uuid = $this->perangkat_model->insert($mapel_uuid, $jenis_file);
            if (!$uuid) {
                $this->session->set_flashdata('error_msg', 'Gagal menyimpan data');
                redirect('perangkat/detail/' . $mapel_uuid);
            }

            if ($this->upload->do_upload('file_perangkat')) {
                $file_name = $this->upload->data('file_name');
                $this->perangkat_model->update_file($uuid, $file_name);
                $this->session->set_flashdata('success_msg', 'File ' . ($jenis_file == 'modul' ? 'Modul' : 'ATP') . ' berhasil diupload');
            } else {
                // Hapus record jika upload gagal
                $this->perangkat_model->delete_by_uuid($uuid);
                $this->session->set_flashdata('error_msg', 'Gagal mengupload file: ' . $this->upload->display_errors());
            }
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
        $can_delete = is_admin_or_superadmin() || $perangkat->guru_uuid == $this->session->userdata('uuid');
        
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
}