<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); error_reporting(E_ALL); ini_set('display_errors',1);
/*
 * This file is part of Auth_Ldap.

    Auth_Ldap is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Auth_Ldap is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Auth_Ldap.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * @author      Greg Wojtak <gwojtak@techrockdo.com>
 * @copyright   Copyright © 2010,2011 by Greg Wojtak <gwojtak@techrockdo.com>
 * @package     Auth_Ldap
 * @subpackage  auth demo
 * @license     GNU Lesser General Public License
 */
date_default_timezone_set ('America/Denver' );
class Auth extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->helper('form');
        $this->load->library('Form_validation');
        $this->load->helper('url');
        $this->load->library('table');
		$this->load->helper('crowd_helper');
    }

    function index() {
        $this->session->keep_flashdata('tried_to');
        $this->login();
    }

    function login($errorMsg = NULL){

		
		$username = NULL;
		$this->session->keep_flashdata('tried_to');
		$crowd = getCrowd();
		$is_authenticated = $crowd['is_authenticated'];
		$crowd = $crowd['instance'];
		if (!$is_authenticated && $this->input->post('username')){
	    // Set up rules for form validation
			$rules = $this->form_validation;
			$rules->set_rules('username', 'Username', 'required|alpha_dash');
            $rules->set_rules('password', 'Password', 'required');
			// Do the login...
			if($rules->run()){
				try{
					$_COOKIE['crowd.token_key'] = $crowd->authenticatePrincipal($this->input->post('username'),  $this->input->post('password'), $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR']);
					setcookie('crowd.token_key', $_COOKIE['crowd.token_key'], time() + 3600, "/");
					$is_authenticated = TRUE;
				}
				catch (Services_Atlassian_Crowd_Exception $e)
				{
					
				}
			}
		}
		//process authenticated users
		if ($is_authenticated) {
			#Make sure user is in the white list of authorized users
			if (!empty($_COOKIE['crowd.token_key']))
				$principal = $crowd->findPrincipalByToken($_COOKIE['crowd.token_key']);
			else
				$principal = $crowd->findPrincipalByToken($_COOKIE['crowd_token_key']);
			foreach ($principal->attributes->SOAPAttribute as $value){
				if ($value->name == 'mail')
					$mail = $value->values->string;
				else if ($value->name == "displayName")
					$name = $value->values->string;
			}
			
			$this->session->set_userdata(array('username'=>$principal->name, 'email'=>$mail, 'cn'=>$name));
			if (in_array($principal->name, $this->config->item('admins')))
				$this->session->set_userdata(array('role'=>'Administrator'));
			if ($this->input->post('page') === 'admin'){
				$this->session->set_userdata(array('admin'=>'true'));
				redirect('admin/');
			}
			else if ($this->input->post('page') === 'optout'){
				redirect('optout/');
			}				
			redirect('main/');
       }
		//form not submitted
		$this->load->view('templates/header');
		$this->load->view('auth/login_form');
		$this->load->view('templates/footer');
		
    }

    function logout() {	
		$crowd = getCrowd();
		$is_authenticated = $crowd['is_authenticated'];
		$crowd = $crowd['instance'];
		if (!empty($_COOKIE['crowd_token_key']))
			$crowd->invalidatePrincipalToken($_COOKIE['crowd_token_key']);
		if (!empty($_COOKIE['crowd.token_key']))
			$crowd->invalidatePrincipalToken($_COOKIE['crowd.token_key']);
		$this->session->sess_destroy();
		unset($_COOKIE['crowd.token_key']);
		unset($_COOKIE['crowd_token_key']);
	
		
		if (!empty($_GET['admin']) && $_GET['admin']=="true")
			redirect('admin/');
		elseif (!empty($_GET['optout']) && $_GET['optout']=="true")
			redirect('optout/');
		$this->load->view('templates/header');
		$this->load->view('auth/login_form');
		$this->load->view('templates/footer');
    }
	
}

?>
