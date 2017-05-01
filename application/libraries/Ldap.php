<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap {
	function __construct($userinfo) {
		$domain = 'CTLINT';
		$server = 'ldap.Mycompany.com';
		$ldaprdn =  $domain . "\\" . $userinfo[0];
		$ldappass = $userinfo[1];
		$ldapconn = ldap_connect($server) or die("Could not connect to LDAP server.");
	    $this->dn="OU=community,DC=Mycompany,DC=com";
		$this->LDAPFieldsToFind = array('memberOf');
		if ($ldapconn)  {
			$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
			if ($ldapbind) {
				$this->ldapconn=$ldapconn;
			} else {
				ldap_close($ldapconn);
				exit;
			}
		}
	}
	
	public function getType($username){
		$filter="sAMAccountName=".$username;
		$sr=ldap_search($this->ldapconn, $this->dn, $filter, $this->LDAPFieldsToFind);
		$info = ldap_get_entries($this->ldapconn, $sr);
		$type="";
		$type_array=$info[0]['memberof'];
		foreach ( $type_array as $value ) {
			 if( substr_count($value, 'cl-members') > 0 ) {
				$type='member';
				break;
			 }
			 else if( substr_count($value, 'cl-employees') > 0 ) {
				$type='employee';
				break;
			 }
			 else if( substr_count($value, 'cl-vendors') > 0 ) {
				$type='vendor';
				break;
			 }
		}
		return $type;
	}
}

