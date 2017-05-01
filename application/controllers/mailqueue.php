<?php
	class Mailqueue extends CI_Controller {
		function __construct() {
			parent::__construct();
			$this->load->library('email');
			$this->load->helper("file");
			$this->load->database();
		}
		function index() {
			$this->notify();
		}
		function notify(){
			set_time_limit (0);
			if (isset($_GET['user'])){
				$config = array('protocol'=>$this->config->protocol, 'smtp_host'=>$this->config->smtp_host,'smtp_port'=>$this->config->smtp_port);
				$this->email->initialize($config);
				$query = $this->db->get_where('mail_queue', array('user'=>$_GET['user']));
					foreach ($query->result() as $row){
						$this->email->clear();
						$this->email->from($row->sender);
						$this->email->to($row->recipient);
						$this->email->subject($row->subject);
						$this->email->message($row->body);
						$this->email->attach($row->file);
						$this->email->send();
					
					}
			}		
		}
	}