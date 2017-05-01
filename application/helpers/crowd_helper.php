<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getCrowd'))
{
    function getCrowd()
    {
        require_once('Services/Atlassian/Crowd.php');
		$CI =& get_instance();
		$CI->load->library('utilities');
		$is_authenticated = FALSE;
		$crowd = new Services_Atlassian_Crowd(array(
			'app_name' => $CI->config->item('crowd_app_name'),
			'app_credential' => $CI->config->item('crowd_app_password'),
			'service_url' => $CI->config->item('crowd_url'),
		));
		$crowd->authenticateApplication();		
		
		if (!empty($_COOKIE['crowd.token_key']) || !empty($_COOKIE['crowd_token_key']))
		{
			if (!empty($_COOKIE['crowd.token_key'])){
				$is_authenticated = $crowd->isValidPrincipalToken(
					$_COOKIE['crowd.token_key'],
					$_SERVER['HTTP_USER_AGENT'],
					$_SERVER['REMOTE_ADDR']
				);
				if ($is_authenticated){
					$principal = $crowd->findPrincipalByToken($_COOKIE['crowd.token_key']);
					//Does the user have permission to access content
					$is_authenticated = $CI->utilities->hasPermission($principal->name);
					if (!$is_authenticated)
						$crowd->invalidatePrincipalToken($_COOKIE['crowd.token_key']);
				}
			}
			else{
				$is_authenticated = $crowd->isValidPrincipalToken(
					$_COOKIE['crowd_token_key'],
					$_SERVER['HTTP_USER_AGENT'],
					$_SERVER['REMOTE_ADDR']
				);
				
				if ($is_authenticated){
					$principal = $crowd->findPrincipalByToken($_COOKIE['crowd_token_key']);
					//Does the user have permission to access content
					$is_authenticated = $CI->utilities->hasPermission($principal->name);
					if (!$is_authenticated)
						$crowd->invalidatePrincipalToken($_COOKIE['crowd_token_key']);
				}
			}
		}
		
		if (isset($principal)){
			foreach ($principal->attributes->SOAPAttribute as $value){
				if ($value->name == 'mail')
					$mail = $value->values->string;
				else if ($value->name == "displayName")
					$name = $value->values->string;
			}
			
			$CI->session->set_userdata(array('username'=>$principal->name, 'email'=>$mail, 'cn'=>$name));
			if (in_array($principal->name, $CI->config->item('admins')))
				$CI->session->set_userdata(array('role'=>'Administrator'));
		}
		return array("instance"=>$crowd, "is_authenticated" =>$is_authenticated);
    }   
}