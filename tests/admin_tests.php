<?php
ini_set("display_errors","1");
class AdminTests extends WebTestCase{
	function authenticate(){
		$this->get('http://itweb-dev.Mycompany.com/subscriptions/auth/?page=admin');
		$this->setField('username','employee-test');
		$this->setField('password','G00gleTablet');
		$this->click("Login");
	}
	function testHomepageNotLoggedIn() {
        $this->get('http://itweb-dev.Mycompany.com/subscriptions/admin');
		$this->assertPattern('/username/');
		$this->assertPattern('/password/');
    }
	function testHomePageLoggedIn(){
		$this->authenticate();
		$this->get('http://itweb-dev.Mycompany.com/subscriptions/admin');
		$this->assertPattern("/Upload File:/");
		$this->assertPattern("/Select one or more themes: /");
		$this->assertPattern("/Email subject line:*/");
		$this->assertPattern("/Email body:*/");
		$this->assertPattern("/All IP/");
		$this->assertPattern("/MarketWatch/");
		$this->assertLink("Logout");
	}
	function testSubmitFormBadValidation(){
		$this->authenticate();
		$this->get('http://itweb-dev.Mycompany.com/subscriptions/admin');
		$this->setField('themes[]', array('1'));
		$this->click("Submit");
		$this->assertPattern("/The Email Subject field is required/");
		$this->assertPattern("/The Email Body field is required/");
		$this->assertPattern("/Upload File:/");
		$this->assertPattern("/Select one or more themes: /");
		$this->assertPattern("/Email subject line:*/");
		$this->assertPattern("/Email body:*/");
		$this->assertPattern("/All IP/");
		$this->assertPattern("/MarketWatch/");
		$this->assertPattern("/value='1'  checked=\"checked\"/");
	}
	function testSuccessfulSubmissionWithFile(){
		$this->authenticate();
		$this->get('http://itweb-dev.Mycompany.com/subscriptions/admin');
		$this->setField('themes[]', array('15'));
		$this->setField('userfile', '/var/www/html/subscriptions/tests/files/1.rtf');
		$this->setField('email_subject', 'test');
		$this->setField('email_body', 'test');
		$this->click("Submit");
		$this->assertPattern("/File notification was a success!/");
		$this->assertPattern("/user1@Mycompany.com/");
	}
	function testSuccessfulSubmissionWithoutFile(){
		$this->authenticate();
		$this->get('http://itweb-dev.Mycompany.com/subscriptions/admin');
		$this->setField('themes[]', array('15'));
		$this->setField('email_subject', 'test');
		$this->setField('email_body', 'test');
		$this->click("Submit");
		$this->assertPattern("/Message has been sent to subscribers!/");
		$this->assertPattern("/user1@Mycompany.com/");
	}
}