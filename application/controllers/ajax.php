<?php

class Ajax extends CI_Controller {

 function __construct() {
	parent::__construct();
  }

  function preview()
  {
	$this->load->model('notify');
	if ($this->input->post('themes')){
		$content_categories='';
		$themes=($this->input->post('themes'));
		$notifyees="These people will be notified: <br/><br/>";
		$n=$this->notify->get_notifyees($content_categories, $themes);
		for ($i=0; $i<count($n); $i++)
			$notifyees.=$n[$i]['email']."<br/>";

		echo $notifyees;
	}
	else
		echo "Please before sure to check a theme.";
  }

  function testSubmit()
  {
	  
	  
  }
}