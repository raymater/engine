<?php
namespace engine;

class Request
{
	public $request_method = null;
	public $request_time = null;
	public $request_time_float = null;
	public $query_string = null;
	public $http_accept = null;
	public $http_accept_charset = null;
	public $http_accept_encoding = null;
	public $http_accept_language = null;
	public $http_connection = null;
	public $http_host = null;
	public $http_referer = null;
	public $http_user_agent = null;
	public $https = null;
	public $remote_addr = null;
	public $remote_host = null;
	public $remote_port = null;
	public $remote_user = null;
	public $redirect_remote_user = null;
	public $script_filename = null;
	public $script_name = null;
	public $request_uri = null;
	public $path_info = null;
	protected $app = null;
	
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
	
	public function getBody() {
		return file_get_contents('php://input');
	}
	
	public function getParsedBody() {
		if(json_decode(file_get_contents('php://input')) != null) {
			return json_decode(file_get_contents('php://input'));
		}
		else {
			if(xml_parse(file_get_contents('php://input')) == 1) {
				return simplexml_load_string(file_get_contents('php://input'));
			}
			else {
				return file_get_contents('php://input');
			}
		}
	}
}