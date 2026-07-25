<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
    {
        // $this->load->view('partials/header');
		// $this->load->view('partials/sidebar');
		// $this->load->view('partials/topbar');
        $this->load->view('home/home');
		// $this->load->view('partials/footer');
    }
}