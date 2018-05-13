<?php
namespace engine;

class Route
{
	protected $method;
	protected $url;
	protected $action;
	protected $args;
	protected $name;
	protected $app;
	protected $middlewares = array();
	
	public function __construct($_method, $_url, $_action, $_args, $_app) {
		if(is_callable($_action)) {
			if(is_array($_args)) {
				$this->method = $_method;
				$this->url = $_url;
				$this->action = $_action;
				$this->args = $_args;
				$this->app = $_app;
			}
			else {
				throw new \Exception("Incorrect args. Type array expected.");
				http_response_code(500);
			}
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
    }
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getFullUrl($_args = array()) {
		if(count($_args) == 0)
		{
			return $this->app->baseURL().$this->url;
		}
		else
		{
			$thisurl = $this->app->baseURL();
			$links = explode("/", $this->url);
			$nbArgs = 0;
			for($i = 0; $i < count($links); $i++) {
				if((substr(urldecode($links[$i]), -1) == "}") && (substr(urldecode($links[$i]), 0, 1) == "{"))
				{
					if(isset($_args[$nbArgs])) {
						$thisurl .= $_args[$nbArgs];
					}
					$nbArgs++;
				}
				else
				{
					$thisurl .= $links[$i];
				}
				$thisurl .= "/";
			}
			
			return $thisurl;
		}
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function getAction() {
		return $this->action;
	}
	
	public function getArgs() {
		return $this->args;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getMiddlewares() {
		return $this->middlewares;
	}
	
	public function setName($_name) {
		$this->name = $_name;
		return $this;
	}
	
	public function add($_action) {
		if(is_callable($_action)) {
			$this->middlewares[] = $_action;
			return $this;
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
	}
}