<?php
namespace phpengine;

class Authentication
{
	protected $credentials = array();
	protected $type;
	protected $actionNoAuth;
	protected $realm;
	
	private function is_assoc($array) {
		foreach (array_keys($array) as $k => $v) {
			if ($k !== $v) {
				return true;
			}
		}
		return false;
	}
	
	/**
		* Create an Authentication object
		*
		* Make an HTTP Authentication.
		*
		* @param string $_type
		*	Type of HTTP Authentication : "BASIC" or "DIGEST" (Default : "BASIC").
		* @param array $_credentials
		*	Associative array representing usernames and passwords (Default : array()).
		* @param mixed $_actionNoAuth
		*	What do you want to do if you are not authenticate ? Function or null expected (function(Request $req, Response $resp, $args, $app) { // Do Something }) (Default : null).
		* @param string $_realm
		*	The Realm string defining the protection space (RFC 2617) (Default : "Restricted Area").
		* @return void
	**/
	public function __construct($_type = "BASIC", $_credentials = array(), $_actionNoAuth = null, $_realm = "Restricted Area") {
		if($_type != "BASIC" && $_type != "DIGEST") {
			$_type = "BASIC";
		}
		
		if(is_array($_credentials)) {
			if($this->is_assoc($_credentials) && $_credentials != array()) {
				if($_actionNoAuth != null && is_callable($_actionNoAuth) == false) {
					throw new \Exception("Action must be null or callable");
					http_response_code(500);
				}
				else {
					$this->credentials = $_credentials;
					$this->type = $_type;
					$this->actionNoAuth = $_actionNoAuth;
					$this->realm = $_realm;
				}
			}
			else {
				throw new \Exception("Credentials must be an associative array");
				http_response_code(500);
			}
		}
		else
		{
			throw new \Exception("Credentials must be an associative array");
			http_response_code(500);
		}
	}
	
	/**
		* Get the type
		*
		* Return the type of Authentication ("BASIC" or "DIGEST").
		*
		* @return string
		*	Type of Authentication.
	**/
	public function getType() {
		return $this->type;
	}
	
	/**
		* Get the credentials
		*
		* Return the credentials.
		*
		* @return array
		*	Associative array.
	**/
	public function getCredentials() {
		return $this->credentials;
	}
	
	/**
		* Get the action to do when you are not authenticate.
		*
		* Return a function or null value.
		*
		* @return mixed
		*	Action or null value.
	**/
	public function getActionNoAuth() {
		return $this->actionNoAuth;
	}
	
	/**
		* Get the Realm
		*
		* Return the Realm string defining the protection space (RFC 2617).
		*
		* @return string
		*	Realm string
	**/
	public function getRealm() {
		return $this->realm;
	}
}