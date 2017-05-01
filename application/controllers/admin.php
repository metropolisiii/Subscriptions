<?php
	class Admin extends CI_Controller {
		function __construct() {
			parent::__construct();
			date_default_timezone_set('America/Denver');
			
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->load->helper('url');
			$this->load->library('email');
			$this->load->helper("file");
			$this->load->helper("crowd_helper");
			$this->load->database();
			$this->load->library('utilities');
		}
		function index() {
			$this->notify();
		}
		

		/*  Main Function */
		function notify(){		
			/* User must be logged in */
			$crowd = getCrowd();
			$is_authenticated = $crowd['is_authenticated'];
		
			if ($is_authenticated && $this->utilities->hasPermission($this->session->userdata('username'),true)){ 
				$date= date('m/d/Y h:iA'); //Date for logging
				$config['upload_path'] = $this->config->item('upload_path');
				$config['allowed_types'] = '*';
				$config['max_size']    = 20000000000;
				$config['remove_spaces'] = false;
				$mailconfig = array('protocol'=>$this->config->protocol, 'smtp_host'=>$this->config->smtp_host,'smtp_port'=>$this->config->smtp_port, "bcc_batch_mode"=>TRUE);
				
				/* Set email options */
				$this->email->initialize($mailconfig);
				$this->email->from('no-reply@Mycompany.com', 'Mycompany Subscriptions');
				$this->email->to('Subscriptions<no-reply@Mycompany.com>');
				$this->email->set_custom_header('Return-Path',$this->config->item('return_path'));
				$this->email->subject($this->input->post('email_subject'));
				$this->email->message($this->input->post('email_body')."Link to File: <a href='".$this->input->post('filelink')."'>".$this->input->post('filelink')."</a><br/><br/>---<br/>".ucwords($this->input->post('email_contact'))."<br/>".($this->input->post('contact_title') != ""?$this->input->post('contact_title')."<br/>":"")."<a href='mailto:".$this->input->post('contact_email')."'>".strtolower($this->input->post('contact_email'))."</a>");
				
				$data='';
				$this->load->model('subscriber');
				$this->load->model('notify');
				$data['user_fullname'] = $this->session->userdata('cn');
				$data['contact_title'] = $this->session->userdata('title');
				$data['contact_email'] = $this->session->userdata('email');
				/* Set form validation rules */
				$this->form_validation->set_rules('themes', 'Focal Areas', 'required');
				$this->form_validation->set_rules('email_subject', 'Email Subject', 'required');
				$this->form_validation->set_rules('email_body', 'Email Body', 'required');
				$this->form_validation->set_rules('email_contact', 'Contact', 'required');
				$this->form_validation->set_rules('filelink', 'Link to File', 'required');
				
				/* Get themes */
				$themes=$this->db->get_where('themes', array('publication' => 0));
								
				/* Prepare themes and publications for output into the view */
				foreach ($themes->result() as $row)
					$data['themes'][]=array('id'=>$row->id, 'name'=>$row->name);
				
				
				/* Data does not validate. Send back to page with errors */
				if ($this->form_validation->run() == FALSE && !isset($_POST['TestSubmit'])){
					$this->load->view('templates/header');
					$this->load->view('notify', $data);
					$this->load->view('templates/footer');					
				}
				/* Data validates */
				else{
					$data['success'] = 'Message has been sent to subscribers!';
					$themes=($this->input->post('themes'));
					$content_categories="";
					
					if(isset($_POST['TestSubmit']))
						$notifyees[]['email'] = $this->session->userdata('email');
					else
						$notifyees=$this->notify->get_notifyees($content_categories, $themes);
					$data['notifyees']="";
					$data['bcc']=array();
					
					/* Put message in log file */
					log_message('info',$date." email blast sent: ".$this->input->post('filelink'));
					
					
					/* Go through each notifyee, put them into the blast email, set a confirmation message, and add message to log file */
					for ($i=0; $i<count($notifyees); $i++){
						$data['notifyees'].=$notifyees[$i]['email']."<br/>";
						$data['bcc'][]=$notifyees[$i]['email'];
						$msg=$this->session->userdata('username')." ".$this->input->ip_address()." Sent ".$this->input->post('filelink')." to ".$notifyees[$i]['email'];
						if( ! file_put_contents($this->config->item('info_log'), $date.": ".$msg."\n",FILE_APPEND)) {
							log_message('info',$date." ".$msg);
						}
					}
				
					$msg = "";
					if ($themes){
						foreach ($themes as $theme)
							$msg = $theme."\n";
					}
					
					/* Log the general information for reports */
					$info = array(
						'sender_name' => $this->session->userdata('cn'),
						'email_subject' => $this->input->post('email_subject'),
						'date_sent' => date('Y-m-d')
					);
					$this->db->insert('logs', $info);
					$log_id = $this->db->insert_id();
					/* Log the themes that were selected for the reports */
					if(!isset($_POST['TestSubmit'])){
						for ($i=0; $i<count($this->input->post('themes')); $i++){
							$info = array(
								'log_id' => $log_id,
								'theme_id' => $this->input->post('themes')[$i]
							);
							$this->db->insert('logs_themes', $info);
						}			
					}
					
					/* Set email notifyees and send */
					$this->email->bcc($data['bcc']);
					$this->email->set_mailtype("html");
					
					$this->email->send();
			
					/* Send data to the view */
					$this->load->view('templates/header');
					$this->load->view('confirmation', $data);
					$this->load->view('templates/footer');		
					
				}
			}
			/* If user is not logged in, redirect them to the login screen */
			else
				redirect('auth/?page=admin');
		}
	
	}