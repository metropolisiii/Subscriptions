<?php 
	ini_set("display_errors","1");
	require_once("../simpletest/autorun.php");
	require_once('../simpletest/web_tester.php');
	
	class SubscriptionTests extends TestSuite {
		 function __construct() {
			parent::__construct();
			$this->addFile('admin_tests.php');
		}
	}