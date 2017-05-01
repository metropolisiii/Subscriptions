<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	class Reports extends CI_Controller {
		function __construct() {
			parent::__construct();
			$this->load->library('email');
			$this->load->model('subscriber');
			$this->load->helper('crowd_helper');
		}
		function index() {
			$this->generate();
		}
		function generate(){
			$this->load->model('report');
			$crowd = getCrowd();
			$is_authenticated = $crowd['is_authenticated'];
			
			if (!$is_authenticated || $this->session->userdata('role') != 'Administrator'){
				redirect('auth');
			}
			$subscribers = $this->report->getSubscribers();
			$data = $this->report->getMarketwatchData();
			$data['subscribers'] = $subscribers;
			$data['themes_report'] = $this->report->getThemesReport();
			//Get all of the subscribers and their subscriptions
			
			$this->load->view('templates/header');
			$this->load->view('reports', $data);
			$this->load->view('templates/footer');
	
		}
	}