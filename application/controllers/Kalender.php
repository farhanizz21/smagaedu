<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
	}

	public function index()
	{
		$data = array(
			'active_nav' => 'kalender'
		);

        $this->load->view('partials/header_tailwind', ['title' => 'Kalender Akademik']);
		$this->load->view('partials/navbar', ['active_nav' => 'kalender']);
        $this->load->view('kalender/kalender', array_merge($data, ['from_controller' => true]));
		$this->load->view('partials/footer_tailwind');
	}
}