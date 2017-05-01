<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Utilities {
		public function getFormFields($data){
			$message='';
			foreach ($$data as $key=>$value){
				if (is_array($value))
					getFormFields($value);
				$message.=$key." : ".$value."\n";
			}
			return $message;
		}

	}