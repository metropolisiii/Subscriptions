<?php

	class Main extends CI_Controller {
		function __construct() {
			parent::__construct();
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->load->helper('url');
			$this->load->helper('file');
			//$this->load->helper('string');
			$this->load->library('email');
			$this->load->database();
			$this->load->model('subscriber');
			$this->load->helper('crowd_helper');
		}
		function index() {
			$this->subscribe();
		}
		
		//main controller
		function subscribe(){
			include("/var/www/subscriptions/misc/settings.php");
			
			$sendmessage=false;
			$subscriber_id=0;
			$crowd = getCrowd();
			$is_authenticated = $crowd['is_authenticated'];
		//	$this->form_validation->set_rules('content_categories', 'Content Categories', 'required');
		//	$this->form_validation->set_rules('themes', 'Themes', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			if (!$is_authenticated){
				redirect('auth');
			}
			//Take care of logged in users. Logged in users with accounts will already have themes they're subscribed to. Grab his or her subscribed themes and store them in an array so checkboxes can be checked for themes he or she is subscribed to.
			if ( $is_authenticated){ 
				$query=$this->db->get_where('subscribers', array('username'=>$this->session->userdata('username')),1);
				if ($query->num_rows()>0){
					$subscriber=$query->row();
					$subscriber_id=$subscriber->id;
				}
			
				//Get themes for logged in users
				//$content_categories=$this->db->get_where('content_categories', array('must_login'=>1));
				$themes=$this->db->get_where('themes', array('publication' => 0));
				$publications=$this->db->get_where('themes', array('publication' => 1));
				$data['email']=$this->session->userdata('email');
				$data['is_authenticated'] = true;
			}
			else{
				//Get themes for non-logged-in users
				//$content_categories=$this->db->get_where('content_categories', array('must_login'=>0));
				if (isset($_POST['email']))
					$data['email'] = $this->input->post('email');
				
				$themes=$this->db->get_where('themes', array('must_login'=>0,'publication'=>0));
				$publications=$this->db->get_where('themes', array('must_login'=>0, 'publication' => 1));
			}			
			
			//foreach ($content_categories->result() as $row)
			//	$data['content_categories'][]=array('id'=>$row->id, 'name'=>$row->name);
			foreach ($themes->result() as $row)
				$data['themes'][$row->id]=array('id'=>$row->id, 'name'=>$row->name, 'description'=>$row->description);
			foreach ($publications->result() as $row)
				$data['publications'][$row->id]=array('id'=>$row->id, 'name'=>$row->name, 'description'=>$row->description);
			
			if ($this->form_validation->run()){
				if ( $is_authenticated){ 
					if (!$user=$this->subscriber->get_item('subscribers', array('username'=>$this->session->userdata('username'))))
						$user=$this->subscriber->insert_item_into_db('subscribers', array('username'=>$this->session->userdata('username'), 'email'=>$this->input->post('email')));						
					else
						$user=$this->subscriber->update_item('subscribers',array('email'=>$this->input->post('email')), array('id'=>$user));
					$this->subscriber->clear('themes_subscribers', array('subscriber_id'=>$user));
					$themes=($this->input->post('themes'));
					$publications=($this->input->post('publications'));
					for ($i=0; $i<count($themes); $i++){
						if ($themes[$i])
							$this->subscriber->insert_item_into_db('themes_subscribers', array('theme_id'=>$themes[$i], 'subscriber_id'=>$user));
					}
					for ($i=0; $i<count($publications); $i++){
						if ($publications[$i])
							$this->subscriber->insert_item_into_db('themes_subscribers', array('theme_id'=>$publications[$i], 'subscriber_id'=>$user));
					}
					$message=$this->session->userdata('cn')."'s subscription has been updated. \n";
					$sendmessage=true;
					$data['success'] = 'Your information has been updated';
				}
				else{ //user not logged in
					//Generate random string. When a user isn't logged in, his email will be put into a holding table as well as a secret randomly generate code that the user's link must match
					$code=random_string('alnum', 15);
					$userid=$this->subscriber->clear('temp_users', array('email'=>$this->input->post('email')));
					if ($userid)
						$this->subscriber->clear('theme_temp_users', array('temp_user_id'=>$userid));
					$id=$this->subscriber->insert_item_into_db('temp_users', array('email'=>$this->input->post('email'), 'code'=>$code));
					
					//Insert subscription choices into holding table
					$themes=($this->input->post('themes'));
					for ($i=0; $i<count($themes); $i++){
						if ($themes[$i])
							$this->subscriber->insert_item_into_db('theme_temp_users', array('theme_id'=>$themes[$i], 'temp_user_id'=>$id));
					}
					$publications=($this->input->post('publications'));
					for ($i=0; $i<count($publications); $i++){
						if ($publications[$i])
							$this->subscriber->insert_item_into_db('theme_temp_users', array('theme_id'=>$publications[$i], 'temp_user_id'=>$id));
					}
					//Send verification email
					$this->email->from('no-reply@Mycompany.com', 'Mycompany Subscriptions');
					$this->email->subject('Complete Your Mycompany Subscription');
					$this->email->set_mailtype("html");
					$body="Please verify that you would like to subscribe to a Mycompany theme. If you did not attempt such a subscription, please ignore this email.<br/><br/>";
					
					//Check if user already exists to warn them before hand.
					$alreadyexists=$this->subscriber->get_item('subscribers', array('email'=>$this->input->post('email')));
					if ($alreadyexists)
						$body.="<b>Note: You already have a Mycompany subscription. If you verify the new subscription, the old one will be overwritten.</b><br/><br/>";
					
					$body.="Please click or copy/paste the following link into your browser's address bar to verify that you are the person that is requesting the subscription<br/><br/>
					<a href='".$this->config->base_url()."main/verify/".$code."/".$id."'>".$this->config->base_url()."main/verify/".$code."/".$id."</a><br/><br/>
					Mycompany Subscriptions";
					$this->email->message($body);
					$this->email->to($this->input->post('email'));
					$this->email->send();
					
					$data['success'] = 'Your information has been updated. You must first verify your email address by going to the email you entered and follwing the instructions to complete your subscription.';
				}
				//$content_categories=($this->input->post('content_categories'));
				
				//$this->subscriber->clear('content_categories_subscribers', array('subscriber_id'=>$user));
				//for ($i=0; $i<count($content_categories); $i++){
				//	if ($content_categories[$i])
				//		$this->subscriber->insert_item_into_db('content_categories_subscribers', array('content_category_id'=>$content_categories[$i], 'subscriber_id'=>$user));
				//}
				
				if ($sendmessage){
					$this->email->from('no-reply@Mycompany.com', 'Mycompany Subscriptions');
					$this->email->subject('A subscriber has updated his/her information');
					
					$formValues = $this->input->post(NULL, TRUE);
					
					foreach ($formValues as $key=>$value){
						$message.=$key." : ";
						if (is_array($value)){
							foreach ($value as $k=>$v){
								//if ($key==='content_categories')
								//	$message.=$data['content_categories'][$v-1]['name'].", ";
								if ($key==='themes')
									$message.=$data['themes'][$v]['name'].", ";
								if ($key==='publications')
									$message.=$data['publications'][$v]['name'].", ";
							}
							$message=substr($message, 0, -2);
							$message.="\n\n" ;
						}
						else
							$message.=$value."\n ";
					}
					
					$this->email->message($message);
					foreach ($notifyees as $notifyee){
						$this->email->to($notifyee);
						$this->email->send();
					}
				}
				$date= date('m/d/Y h:iA');
				$msg=$this->session->userdata('username')." ".$this->input->ip_address()." updated his subscription ";
				if( ! file_put_contents($this->config->item('info_log'), $date.": ".$msg."\n",FILE_APPEND)) {
					log_message('info',$date." ".$msg);
				}
			}	
			
			if (isset($subscriber_id)){
			//	$query=$this->db->get_where('content_categories_subscribers', array('subscriber_id'=>$subscriber_id));
			//	foreach ($query->result() as $row){
			//		$data['content_categories_subscribers'][$row->content_category_id]=$row->content_category_id;
			//	}
				$query=$this->db->get_where('themes_subscribers', array('subscriber_id'=>$subscriber_id));
				foreach ($query->result() as $row){
					$data['themes_subscribers'][$row->theme_id]=$row->theme_id;
				}
			}
		//	else if (isset($this->input->post('email'))){
				//$query=$this->db->get_where('theme_temp_users', array('temp_user_id'=>$this->input->post('email')));
				//foreach ($query->result() as $row){
				//	$data['themes_subscribers'][$row->theme_id]=$row->theme_id;
				//}
			//}
			
			$this->load->view('templates/header');
			$this->load->view('subscriber', $data);
			$this->load->view('templates/footer');
		}
		
		//Verification
		function verify(){
			$themes="Themes: ";
			$publications="Publications: ";
			include("/var/www/subscriptions/misc/settings.php");
			$query=$this->db->get_where('temp_users', array('code'=>$this->uri->segment(3), 'id'=>$this->uri->segment(4)),1);
			if ($query->num_rows()>0){
				$subscriber=$query->row();
				$userid=$this->subscriber->clear('subscribers', array('email'=>$subscriber->email));
				if ($userid)
					$this->subscriber->clear('themes_subscribers', array('subscriber_id'=>$userid));
				$id=$this->subscriber->insert_item_into_db('subscribers', array('email'=>$subscriber->email));
				$this->subscriber->clear('temp_users', array('id'=>$subscriber->id));
				$query2=$this->db->get_where('theme_temp_users', array('temp_user_id'=>$subscriber->id));
				if ($query2->num_rows()>0){
					foreach ($query2->result() as $row){
						$this->subscriber->insert_item_into_db('themes_subscribers', array('theme_id'=>$row->theme_id, 'subscriber_id'=>$id));
						$theme=$this->db->get_where('themes', array('id'=>$row->theme_id),1);
						if ($theme->num_rows() > 0){
							$t=$theme->row();
							$themes.=$t->name.", ";
						}
						$this->subscriber->clear('theme_temp_users', array('id'=>$row->id));
					}
					$themes=substr($themes, 0, -2);
				}
				$data['success']="Your subscription information has been updated!";
				
				$this->email->from('no-reply@Mycompany.com', 'Mycompany Subscriptions');
				$this->email->subject('A subscriber has updated his/her information');
				$message=$subscriber->email." (non-account user) subscription has been updated. \n";
				$message.=$themes."\n";
				$this->email->message($message);
				foreach ($notifyees as $notifyee){
					$this->email->to($notifyee);
					$this->email->send();
				}

				$this->load->view('templates/header');
				$this->load->view('subscriber_error', $data);
				$this->load->view('templates/footer');
			}
			else{
				$data['error']="Your subscription information was not found. You may have either already verified this subscription or the information has been purged due to inactivity";
				$this->load->view('templates/header');
				$this->load->view('subscriber_error', $data);
				$this->load->view('templates/footer');
			}
		}
	}