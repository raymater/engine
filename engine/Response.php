<?php
namespace engine;

class Response
{
	protected $ContentType;
	protected $Access_Control_Allow_Origin;
	protected $codeHTTP;
	protected $app;
	
	public function __construct($_app) {
		$this->app = $_app;
		
		if($this->app->isDebug() === true) {
			if(!headers_sent()) {
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
	
	public function AccessControlAllowOrigin($state = true) {
		$this->Access_Control_Allow_Origin = $state;
		return $this;
	}
	
	public function withHeader($_name, $_value) {
		header($_name.": ".$_value);
		return $this;
	}
	
	public function withStatus($_code = 200) {
		$this->codeHTTP = $_code;
		return $this;
	}
	
	public function writeXML($_xml = "")
	{
		header("Content-Type: application/xml");
		http_response_code($this->codeHTTP);
		$xml = new \SimpleXMLElement($_xml);
		echo $xml->asXML();
		return $this;
	}
	
	public function writeJSON($_json = "{}")
	{
		header("Content-Type: application/json");
		http_response_code($this->codeHTTP);
		echo (json_encode(json_decode($_json), JSON_UNESCAPED_UNICODE));
		return $this;
	}
	
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