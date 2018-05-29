<?php
namespace engine;

class Request
{
	protected $request_method = null;
	protected $request_time = null;
	protected $request_time_float = null;
	protected $query_string = null;
	protected $http_accept = null;
	protected $http_accept_charset = null;
	protected $http_accept_encoding = null;
	protected $http_accept_language = null;
	protected $http_connection = null;
	protected $http_host = null;
	protected $http_referer = null;
	protected $http_user_agent = null;
	protected $https = null;
	protected $remote_addr = null;
	protected $remote_host = null;
	protected $remote_port = null;
	protected $remote_user = null;
	protected $redirect_remote_user = null;
	protected $script_filename = null;
	protected $script_name = null;
	protected $request_uri = null;
	protected $path_info = null;
	protected $app = null;
	
	/**
		* Create the Request object
		*
		* Make the HTTP Request object.
		*
		* @param Application $_app
		*	The current application
		* @return void
	**/
	public function __construct($_app) {
		$this->app = $_app;
		
		if(isset($_SERVER["REQUEST_METHOD"])) {
			$this->request_method = $_SERVER["REQUEST_METHOD"];
		}
		if(isset($_SERVER["REQUEST_TIME"])) {
			$this->request_time = $_SERVER["REQUEST_TIME"];
		}
		if(isset($_SERVER["REQUEST_TIME_FLOAT"])) {
			$this->request_time_float = $_SERVER["REQUEST_TIME_FLOAT"];
		}
		if(isset($_SERVER["QUERY_STRING"])) {
			$this->query_string = $_SERVER["QUERY_STRING"];
		}
		if(isset($_SERVER["HTTP_ACCEPT"])) {
			$this->http_accept = $_SERVER["HTTP_ACCEPT"];
		}
		if(isset($_SERVER["HTTP_ACCEPT_CHARSET"])) {
			$this->http_accept_charset = $_SERVER["HTTP_ACCEPT_CHARSET"];
		}
		if(isset($_SERVER["HTTP_ACCEPT_ENCODING"])) {
			$this->http_accept_encoding = $_SERVER["HTTP_ACCEPT_ENCODING"];
		}
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$this->http_accept_language = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		}
		if(isset($_SERVER["HTTP_CONNECTION"])) {
			$this->http_connection = $_SERVER["HTTP_CONNECTION"];
		}
		if(isset($_SERVER["HTTP_HOST"])) {
			$this->http_host = $_SERVER["HTTP_HOST"];
		}
		if(isset($_SERVER["HTTP_REFERER"])) {
			$this->http_referer = $_SERVER["HTTP_REFERER"];
		}
		if(isset($_SERVER["HTTP_USER_AGENT"])) {
			$this->http_user_agent = $_SERVER["HTTP_USER_AGENT"];
		}
		if(isset($_SERVER["HTTPS"])) {
			$this->https = $_SERVER["HTTPS"];
		}
		if(isset($_SERVER["REMOTE_ADDR"])) {
			$this->remote_addr = $_SERVER["REMOTE_ADDR"];
		}
		if(isset($_SERVER["REMOTE_HOST"])) {
			$this->remote_host = $_SERVER["REMOTE_HOST"];
		}
		if(isset($_SERVER["REMOTE_PORT"])) {
			$this->remote_port = $_SERVER["REMOTE_PORT"];
		}
		if(isset($_SERVER["REMOTE_USER"])) {
			$this->remote_user = $_SERVER["REMOTE_USER"];
		}
		if(isset($_SERVER["REDIRECT_REMOTE_USER"])) {
			$this->redirect_remote_user = $_SERVER["REDIRECT_REMOTE_USER"];
		}
		if(isset($_SERVER["SCRIPT_FILENAME"])) {
			$this->script_filename = $_SERVER["SCRIPT_FILENAME"];
		}
		if(isset($_SERVER["SCRIPT_NAME"])) {
			$this->script_name = $_SERVER["SCRIPT_NAME"];
		}
		if(isset($_SERVER["REQUEST_URI"])) {
			$this->request_uri = $_SERVER["REQUEST_URI"];
		}
		if(isset($_SERVER["PATH_INFO"])) {
			$this->path_info = $_SERVER["PATH_INFO"];
		}
	}
	
	/**
		* Return a value from a Request property
		*
		* Magic method returning a value from a Request property.
		*
		* @param string $name
		*	Name of property
		* @return mixed
		*	Value of property
	**/
	public function __get($name)
    {
        return $this->$name;
    }
	
	/**
		* Get the request body
		*
		* Return the content of request body.
		*
		* @return string
		*	Content of request body
	**/
	public function getBody() {
		return file_get_contents('php://input');
	}
	
	/**
		* Get the parsed request body
		*
		* Return the parsed request body : JSON object, XML object (SimpleXML object), YAML object or RAW request body data (string).
		*
		* @return mixed
		*	Object of request parsed
	**/
	public function getParsedBody() {
		if(json_decode(file_get_contents('php://input')) != null) {
			return json_decode(file_get_contents('php://input'));
		}
		else {
			if(xml_parse(file_get_contents('php://input')) == 1) {
				return simplexml_load_string(file_get_contents('php://input'));
			}
			else {
				if(yaml_parse(file_get_contents('php://input')) != false) {
					return yaml_parse(file_get_contents('php://input'));
				}
				else {
					return file_get_contents('php://input');
				}
			}
		}
	}
	
	protected function filter($array = array()) {
		if(count($array) > 0) {
			foreach($array as $param => $value) {
				if(is_null($value) === true) {
					$array[$param] = $value;
				}
				else {
					if(is_int($value) === true) {
						$array[$param] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
					}
					else {
						if(is_bool($value) === true) {
							$array[$param] = $value;
						}
						else {
							if(is_float($value) === true) {
								$array[$param] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
							}
							else {
								if(is_string($value) === true) {
									$array[$param] = filter_var(addslashes($value), FILTER_SANITIZE_STRING);
								}
								else {
									$array[$param] = $value;
								}
							}
						}
					}
				}
			}
		}
		
		return $array;
	}

	/**
		* Sanitize all datas on $_GET
		*
		* Filter all values on $_GET array
		*
		* @return array
		*	Return the filter $_GET array
	**/
	public function filterGET() {
		$_GET = $this->filter($_GET);
		return $_GET;
	}
	
	/**
		* Sanitize all datas on $_POST
		*
		* Filter all values on $_POST array
		*
		* @return array
		*	Return the filter $_POST array
	**/
	public function filterPOST() {
		$_POST = $this->filter($_POST);
		return $_POST;
	}
}