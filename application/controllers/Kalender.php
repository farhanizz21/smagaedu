<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// $this->load->model('kalender_model');
		$this->load->library('form_validation');
		$this->load->model('auth_model');
		if(!$this->auth_model->current_user()){
			redirect('login');
		}
	}

	public function index()
	{
		// $kalender = $this->kalender_model->get_all();

		$data = array(
			// 'kalender' => $kalender,
			'active_nav' => 'kalender'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Kalender Akademik']);
		$this->load->view('partials/navbar', ['active_nav' => 'kalender']);
        $this->load->view('kalender/kalender', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
}