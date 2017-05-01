  
<?php
/**
 * Services_Atlassian_Crowd Client
 *
 *
 * @category  Services
 * @package   Services_Atlassian_Crowd
 */
require_once dirname(__FILE__) . '/crowdConfig.php';
ob_start();


class CrowdClient
{
    private $crowd = null;
    private $_options;
    private $_token_app;
    private $_token_user;
    
    public function __construct() {
       // no-op;
    }
    public function initConnection()
    {
        // create a connection object
        $this->_options = $GLOBALS['crowd_options'];
	$credentials = new Services_Atlassian_Crowd_ApplicationSoapCredentials(
        	$this->_options['service_url'],
        	$this->_options['app_name'],
        	$this->_options['app_credential']
    	);
        try {
            $this->crowd = new Services_Atlassian_Crowd($credentials);
        } catch (Services_Atlassian_Crowd_Exception $e) {
            print($e->getMessage());
        }
        
        // get an authentication token
        $this->_token_app = $this->crowd->authenticateApplication(
            $this->_options['app_name'],
            $this->_options['app_credential']
            );
        // (is_string($this->_token_app));
        
        
    }
    public function authenticateUser($username,$password) {
	// authenticate a principal
        $this->_token_user = $this->crowd->authenticatePrincipal($username, 
                                                                 $password,
                                                                 $this->_options['user_agent'], 
                                                                 $this->_options['remote_address']);
	
    }
    public function closeConn()
    {
        unset($this->crowd);
    }
    
    public function isValidToken($ssoToken) {
	$remoteAddress = $this->get_client_ip();
	if(isset($ssoToken)) {
	    // test that the token is revoked
            $result = $this->crowd->isValidPrincipalToken($ssoToken,
                                                      $this->_options['user_agent'], 
                                                      $remoteAddress);
	    
	}
	else {
	
	// test that the token is revoked
        $result = $this->crowd->isValidPrincipalToken($this->_token_user,
                                                      $this->_options['user_agent'], 
                                                      $remoteAddress);
	}
	return $result;
    }
    public function getToken() {
	return $this->_token_user;
    }
    public function setToken($token) {
	$this->_token_user = $token;
    }
    public function getCrowd() {
	return $this->crowd;
    }
    
    // Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    
       //  $ipaddress = getenv('REMOTE_ADDR');
//  $ipaddress = $_SERVER['REMOTE_ADDR'];
$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];   
 return $ipaddress;
    }
}

 ob_end_flush();
?>

