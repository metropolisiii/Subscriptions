<?php
	
	class Optout extends CI_Controller {
		function __construct() {
			parent::__construct();
			$this->load->helper('form');
			$this->load->database();
			$this->load->library('form_validation');
			$this->load->helper('url');
			$this->load->library('email');
			$this->load->helper('crowd_helper');
		}
		function index() {
			$this->optout();
		}
		function optout(){
			$crowd = getCrowd();
			$is_authenticated = $crowd['is_authenticated'];
			if ( $is_authenticated){ 
				$data[]=array();
				if (!empty($_POST['submitted'])){
					if ($this->input->post('submit')==="Cancel"){
						redirect("auth/logout/?optout=true");
					}
					$query = $this->db->get_where('themes', array('name'=>'MarketWatch'), 1);
					if ($query->num_rows() > 0){
						foreach ($query->result() as $row){
							$theme_id = $row->id;
							break;
						}
					} 
					$query = $this->db->get_where('subscribers', array('username'=>$this->session->userdata('username')), 1);
					if ($query->num_rows() > 0){
						foreach ($query->result() as $row)
							$subscriber_id = $row->id;
					} 
					if (!empty($theme_id) && !empty($subscriber_id) && !empty($_POST['optout'])){
						$this->db->delete('themes_subscribers', array('theme_id'=>$theme_id, 'subscriber_id'=>$subscriber_id));
						include("/var/www/subscriptions/misc/settings.php");
						$data['success']='true';
						$this->email->from('no-reply@Mycompany.com', 'Mycompany Subscriptions');
						$this->email->subject('A subscriber has opted out of MarketWatch');
						$message=$this->session->userdata('username')." has opted out of MarketWatch.";						
						$this->email->message($message);
						foreach ($notifyees as $notifyee){
							$this->email->to($notifyee);
							$this->email->send();
						}
						$this->load->view('templates/header');
						$this->load->view('optout', $data);
						$this->load->view('templates/footer');
						return;
					}
					else{
						$data['success']='false';
						$this->load->view('templates/header');
						$this->load->view('optout', $data);
						$this->load->view('templates/footer');
						return;
					}					
				}
				
				$this->load->view('templates/header');
				$this->load->view('optout', $data);
				$this->load->view('templates/footer');	
			}
			else
				redirect('auth/?page=optout');
		}
	}