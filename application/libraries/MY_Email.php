<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email {
   public function __construct()
    {
        parent::__construct();
    }
 public function set_custom_header($header_name='', $header_value = '')
 {
  if($header_name=='') {
   return FALSE;
  }

  $this->_set_header($header_name, $header_value);

 }
 protected function _send_with_mail(){
	$return_path=(isset($this->_headers['Return-Path'])?$this->clean_email($this->_headers['Return-Path']):$this->clean_email($this->_headers['From']));
		if ($this->_safe_mode == TRUE)
		{
			if ( ! mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str))
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			// most documentation of sendmail using the "-f" flag lacks a space after it, however
			// we've encountered servers that seem to require it to be in place.
			if ( ! mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str, "-f ".$return_path))
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
}