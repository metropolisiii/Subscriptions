<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
include ('/var/www/subscriptions/application/helpers/jwt/JWT.php');
if ( ! function_exists('createToken')){
	function createToken($userid){
		$tokenId = base64_encode(mcrypt_create_iv(32));
		$issuedAt = time();
		$notBefore = $issuedAt;
		$expire = $notBefore + 86400;
		$serverName = "itweb.Mycompany.com";
		
		$data = [
			'iat' => $issuedAt,
			'jti' => $tokenId,
			'iss' => $serverName,
			'nbf' => $notBefore,
			'exp' => $expire,
			'data' => [
				'username' => $userid
			]
		];
		$CI =& get_instance();
		$secretKey = base64_decode($CI->config->item('encryption_key'));
		$jwt = JWT::encode(
			$data,
			$secretKey,
			'HS512'
		);
		$unencodedArray = ['jwt' => $jwt];
		return $unencodedArray;
	}
	
	function validateToken($token){
		$CI =& get_instance();
		try{
			$secretKey = base64_decode($CI->config->item('encryption_key'));
			$token = JWT::decode($token, $secretKey, array('HS512'));
			return $token;
		}
		catch (Exception $e){
			return 401;
		}
	}
}