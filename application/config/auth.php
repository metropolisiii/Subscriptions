<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
        $this->load->library('Auth_Ldap');
        $this->load->helper('url');
        $this->load->library('table');
    }

    function index() {
        $this->session->keep_flashdata('tried_to');
        $this->login();
    }

    function login($errorMsg = NULL){
		
        $this->session->keep_flashdata('tried_to');
        if(!$this->auth_ldap->is_authenticated()) {
	   // Set up rules for form validation
            $rules = $this->form_validation;
            $rules->set_rules('username', 'Username', 'required|alpha_dash');
            $rules->set_rules('password', 'Password', 'required');

            // Do the login...
            if($rules->run() && $this->auth_ldap->login(
                    $rules->set_value('username'),
                    $rules->set_value('password'))) {
				// Login WIN!
                if($this->session->flashdata('tried_to')) {
                    redirect($this->session->flashdata('tried_to'));
                }else {
					echo strpos($_SERVER['HTTP_REFERER'], 'admin')>0;
					exit;
					if ($this->input->post('page') === 'admin'){
						//Check if admin privilidges
						$data=array(
							'admin'=>true
						);
						$this->session->set_userdata($data);
						redirect('admin/');
					}
                  	redirect('main/');
                }
            }else {
				if ($this->input->post('page')==='admin')
					redirect('admin/');
                // Login FAIL
				$this->load->view('templates/header');
                $this->load->view('auth/login_form', array('login_fail_msg'
                                        => 'Error with LDAP authentication.'));
				$this->load->view('templates/footer');
						
			}
        }else {
                // Already logged in...
                if ($this->session->userdata('admin'))
					redirect('admin/');
				redirect('main/');
        }
    }

    function logout() {
        if($this->session->userdata('logged_in')) {
            $admin=false;
			$data['name'] = $this->session->userdata('cn');
            $data['username'] = $this->session->userdata('username');
            $data['logged_in'] = TRUE;
			if ($this->session->userdata('admin'))
				$admin=true;
            $this->auth_ldap->logout();
        } else {
            $data['logged_in'] = FALSE;
        }
			if ($admin)
				redirect('admin/');
           $this->load->view('templates/header');
			$this->load->view('auth/login_form');
			$this->load->view('templates/footer');
    }
}

?>
