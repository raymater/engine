<?php
namespace engine;

class Response
{
	protected $ContentType;
	protected $Access_Control_Allow_Origin;
	protected $codeHTTP;
	
	public function __construct() {
		$this->ContentType = "text/html; charset=utf-8";
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
	
	public function withStatus($_code) {
		$this->codeHTTP = $_code;
		return $this;
	}
	
	public function write($_write = "") {
		if($this->Access_Control_Allow_Origin == true) {
			header("Access-Control-Allow-Origin: *");
		}
		header("Content-Type: ".$this->ContentType);
		http_response_code($this->codeHTTP);
		
		echo $_write;
		return $this;
	}
}