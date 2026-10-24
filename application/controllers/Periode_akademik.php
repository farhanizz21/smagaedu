<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periode_akademik extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('periode_akademik_model');
	}

	public function index()
	{
		$this->render_page();
	}

	public function simpan()
	{
		$this->require_admin_or_superadmin();

		$this->form_validation->set_rules(
			'pekan',
			'Pekan',
			'required|integer|greater_than_equal_to[1]|less_than_equal_to[20]'
		);
		$this->form_validation->set_rules('semester', 'Semester', 'required|in_list[1,2]');

		if ($this->form_validation->run() === FALSE) {
			$this->render_page();
			return;
		}

		$updated = $this->periode_akademik_model->update_current(
			(int) $this->input->post('pekan'),
			(int) $this->input->post('semester')
		);

		if (!$updated) {
			$this->session->set_flashdata('error_msg', 'Periode akademik gagal disimpan. Silakan coba kembali.');
			redirect('periode_akademik');
			return;
		}

		$this->session->set_flashdata('success_msg', 'Pekan dan semester saat ini berhasil diperbarui.');
		redirect('periode_akademik');
	}

	private function render_page()
	{
		$data = array(
			'title' => 'Pekan & Semester',
			'active_nav' => 'periode_akademik',
			'periode' => $this->periode_akademik_model->get_current()
		);

		$this->load->view('partials/header_tailwind', $data);
		$this->load->view('partials/navbar', $data);
		$this->load->view('periode_akademik/index', $data);
		$this->load->view('partials/footer_tailwind');
	}
}
