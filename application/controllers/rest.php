<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Rest extends CI_Controller {
	function __construct() {
		parent::__construct();
		date_default_timezone_set('America/Denver');
		
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('Auth_Ldap');
		$this->load->helper('jwt_helper');
	}
	public function authenticate(){
		if ($this->input->server('REQUEST_METHOD') == "POST"){
			if ($this->auth_ldap->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
				$token = createToken($_SERVER['PHP_AUTH_USER']);
				header('Content-Type: application/json');
				echo json_encode($token);
				http_response_code(200);
				return true;
			}
			http_response_code(403);
			return false;
		}
		http_response_code(400);
		return false;
	}
	
	public function themes(){
		header('Content-Type: application/json');
		if ($this->input->server('REQUEST_METHOD') == "GET"){
			$results_array = array("themes" => array(),"publications" => array());
			
			#Get the list of themes
			$query = $this->db->query("SELECT * FROM themes WHERE publication = 0 ORDER BY name");			
			foreach ($query->result() as $row)
				$results_array['themes'][] = array("name" => $row->name, "id" => $row->id);
			
			#Get the list of all publications
			$query = $this->db->query("SELECT * FROM themes WHERE publication = 1 ORDER BY name");
			foreach ($query->result() as $row)
				$results_array['publications'][] = array("name" => $row->name, "id" => $row->id);
			echo json_encode($results_array);
			http_response_code(200);
			return true;
		}
		http_response_code(400);
		return false;
	}
	
	private function verifyToken($headers){
		if (!isset($headers['Authorization']))
			return array("response_code" => 401);				
		$authHeader = $headers['Authorization'];
		if ($authHeader){
			list($jwt) = sscanf($authHeader, 'Token %s');
			if ($jwt){
				$response = validateToken($jwt);
				if (is_object($response)){
					return array("response_code" => 200, "response" => $response);
				}
			}
		}
		return array("response_code" => 403);
	}
	
	public function subscriptions(){
		header('Content-Type: application/json');
		if ($this->input->server('REQUEST_METHOD') == "GET"){
			$headers = $this->input->request_headers();
			$response = $this->verifyToken($headers);
			if ($response['response_code'] == 200){
				#Get subscriptions belonging to user
				$username = $response['response']->data->username;
				$subscriptions = $this->db->query("SELECT t.id FROM themes_subscribers ts INNER JOIN subscribers s ON ts.subscriber_id = s.id INNER JOIN themes t ON ts.theme_id = t.id WHERE s.username='".$username."'");
				$results_array = array();
				foreach ($subscriptions->result() as $row)
					$results_array[] = $row->id;
				echo json_encode($results_array);
				http_response_code(200);
				return true;
			}
			else{ 
				http_response_code($response['response_code']);
				return false;
			}
		}
		if ($this->input->server('REQUEST_METHOD') == "POST"){
			$headers = $this->input->request_headers();
			$response = $this->verifyToken($headers);
			if ($response['response_code'] == 200){
				$username = $response['response']->data->username;
				$id = $this->input->post('id');
				$checked = $this->input->post('checked');
				$query = $this->db->query("SELECT id from subscribers WHERE username = '{$username}'");
				$row = $query->row();
				if (isset($row)){
					if ($checked == 'true')
						$this->db->query("INSERT INTO themes_subscribers values (null, {$id}, ".$row->id.")");
					else
						$this->db->query("DELETE FROM themes_subscribers where theme_id = {$id} AND subscriber_id = ".$row->id);
				}
				http_response_code(200);
				return true;
			}
			else{ 
				http_response_code($tokenverified['response_code']);
				return false;
			}
		}
	}
}