<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	class Utilities {
		function __construct() {
			$this->ci =& get_instance();
		}
		
		public function getFormFields($data){
			$this->load->database();
			$message='';
			foreach ($data as $key=>$value){
				$message.=$key." : ";
				if (is_array($value)){
					foreach ($value as $k=>$v)
						$message.=$v.", ";
					$message=substr($message, 0, -1);
					$message.="\n";
				}
				else
					$message.=$value."\n";
			}
			return $message;
		}
		public function substr_in_array($haystack, $needle){

			  $found = array();
			 
				// cast to array 
				$needle = (array) $needle;
			 
				// map with preg_quote 
				$needle = array_map('preg_quote', $needle);
			 
				// loop over  array to get the search pattern 
				FOREACH ($needle AS $pattern)
				{
					IF (COUNT($found = PREG_GREP("/$pattern/", $haystack)) > 0) {
						RETURN $found;
					}
				}
			 
				// if not found 
				RETURN FALSE;
		}
		public function hasPermission($username, $admin=false){
			$in_group = false;
			$adServer = $this->ci->config->item('adServer');
			$ldapconn = ldap_connect($adServer);
			$ldapbind = ldap_bind($ldapconn, $this->ci->config->item('proxy_user'), $this->ci->config->item('proxy_pass')); 
			$dn="OU=community,DC=Mycompany,DC=com";
			$filter="sAMAccountName=".$username;
			$sr=ldap_search($ldapconn, $dn, $filter);			
			if ($ldapbind) { //Yay! You made it!
				$sr=ldap_search($ldapconn, $dn, $filter);
				$info = ldap_get_entries($ldapconn, $sr);
				if ($admin)
					$groups = $this->ci->config->item('admin_groups');
				else
					$groups = $this->ci->config->item('whitelist');
				foreach($groups as $group){ //Loop through acceptable groups
					if ($this->substr_in_array($info[0]['memberof'], $group)){ //You are authenticated. Congrats!
						$in_group = true;
					}
				}
			}
			return $in_group;
		}
	}