<?php
namespace phpengine;

class Response
{
	protected $ContentType;
	protected $Access_Control_Allow_Origin;
	protected $codeHTTP;
	protected $app;
	
	/**
		* Create the Response object
		*
		* Make the HTTP Response object. Desactivate cache control if the is on debug mode. Set the Content-Language header with the defined language on current app. By default set HTML Content-Type and 200 HTTP Code.
		*
		* @param Application $_app
		*	The current application
		* @return void
	**/
	public function __construct($_app) {
		$this->app = $_app;
		
		if($this->app->isDebug() === true) {
			if(!headers_sent()) {
				header("Age: 0");
				header("Expires: -1");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Pragma: no-cache");
			}
		}
		
		$this->ContentType = "text/html; charset=utf-8";
		if(!headers_sent()) {
			header("Content-Language: ".$this->app->getLang());
		}
		$this->Access_Control_Allow_Origin = false;
		$this->codeHTTP = 200;
	}
	
	/**
		* Set the Access-Control-Allow-Origin HTTP Header
		*
		* Allow (true) or disallow (false) access the ressource on all origins.
		*
		* @param bool $state
		*	Set true to allow access the ressource on all origins (by default : true).
		* @return Response
		*	The object HTTP Response
	**/
	public function AccessControlAllowOrigin($state = true) {
		$this->Access_Control_Allow_Origin = $state;
		return $this;
	}
	
	/**
		* Set HTTP Header
		*
		* Set a specific HTTP Header with the name and the value.
		*
		* @param string $_name
		*	Name of header (by default : "").
		* @param string $_value
		*	Value of header (by default : "").
		* @return Response
		*	The object HTTP Response
	**/
	public function withHeader($_name = "", $_value = "") {
		header($_name.": ".$_value);
		return $this;
	}
	
	/**
		* Set the HTTP code
		*
		* Set the HTTP response code.
		*
		* @param int $_code
		*	HTTP code (by default : 200)
		* @return Response
		*	The object HTTP Response
	**/
	public function withStatus($_code = 200) {
		$this->codeHTTP = $_code;
		return $this;
	}
	
	/**
		* Write XML content
		*
		* Display XML content by string
		*
		* @param string $_xml
		*	XML valid string (by default : "")
		* @return Response
		*	The object HTTP Response
	**/
	public function writeXML($_xml = "")
	{
		if($this->Access_Control_Allow_Origin == true) {
			header("Access-Control-Allow-Origin: *");
		}
		header("Content-Type: application/xml");
		http_response_code($this->codeHTTP);
		$xml = new \SimpleXMLElement($_xml);
		echo $xml->asXML();
		return $this;
	}
	
	/**
		* Write JSON content
		*
		* Display JSON content by string
		*
		* @param string $_json
		*	JSON valid string (by default : "{}")
		* @return Response
		*	The object HTTP Response
	**/
	public function writeJSON($_json = "{}")
	{
		if($this->Access_Control_Allow_Origin == true) {
			header("Access-Control-Allow-Origin: *");
		}
		header("Content-Type: application/json");
		http_response_code($this->codeHTTP);
		echo (json_encode(json_decode($_json), JSON_UNESCAPED_UNICODE));
		return $this;
	}
	
	/**
		* Write YAML content
		*
		* Display YAML content by string
		*
		* @param string $_yaml
		*	YAML valid string (by default : "")
		* @return Response
		*	The object HTTP Response
	**/
	public function writeYAML($_yaml = "")
	{
		if($this->Access_Control_Allow_Origin == true) {
			header("Access-Control-Allow-Origin: *");
		}
		header("Content-Type: application/x-yaml");
		http_response_code($this->codeHTTP);
		echo (yaml_emit(yaml_parse($_yaml)));
		return $this;
	}
	
	/**
		* Write content
		*
		* Display something you want to show...
		*
		* @param string $_write
		*	Something would you want to display (by default : "")
		* @return Response
		*	The object HTTP Response
	**/
	public function write($_write = "") {
		if($this->Access_Control_Allow_Origin == true) {
			header("Access-Control-Allow-Origin: *");
		}
		if(!headers_sent()) {
			header("Content-Type: ".$this->ContentType);
		}
		http_response_code($this->codeHTTP);
		
		echo $_write;
		return $this;
	}
}