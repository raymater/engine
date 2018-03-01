<?php
namespace engine;

class Application
{
	protected $debug = false;
	protected $routes = array();
	protected $route404 = null;
	protected $_baseURL = null;
	
	public function __construct($_config) {
		$this->routes = array();
		
		if(array_key_exists("debug", $_config)) {
			if($_config["debug"] == true) {
				$this->debug = true;
			}
		}
		
		if($this->debug == true) {
			error_reporting(E_ALL);
		}
		else {
			error_reporting(0);
		}
		
		$this->_baseURL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
	
	public function get($_url, $_action) {
		if($_url != "/")
		{
			$links = explode("/", $_url);
			
			$fullurl = "";
			$args = array();
			
			for($i = 0; $i < count($links); $i++) {
				if($links[$i] != "")
				{
					$fullurl .= "/";
					if((substr($links[$i], -1) == "}") && (substr($links[$i], 0, 1) == "{")) {
						$args[] = substr($links[$i], 1, -1);
						$fullurl .= $links[$i];
					}
					else {
						$fullurl .= urlencode($links[$i]);
					}
				}
			}
			$route = new Route("GET", $fullurl, $_action, $args);
			$this->routes[] = array($route);
			return $route;
		}
		else
		{
			$route = new Route("GET", "/", $_action, array());
			$this->routes[] = array($route);
			return $route;
		}
	}
	
	public function post($_url, $_action) {
		if($_url != "/")
		{
			$links = explode("/", $_url);
			
			$fullurl = "";
			$args = array();
			
			for($i = 0; $i < count($links); $i++) {
				if($links[$i] != "")
				{
					$fullurl .= "/";
					if((substr($links[$i], -1) == "}") && (substr($links[$i], 0, 1) == "{")) {
						$args[] = substr($links[$i], 1, -1);
						$fullurl .= $links[$i];
					}
					else {
						$fullurl .= urlencode($links[$i]);
					}
				}
			}
			$route = new Route("POST", $fullurl, $_action, $args);
			$this->routes[] = array($route);
			return $route;
		}
		else
		{
			$route = new Route("POST", "/", $_action, array());
			$this->routes[] = array($route);
			return $route;
		}
	}
	
	public function put($_url, $_action) {
		if($_url != "/")
		{
			$links = explode("/", $_url);
			
			$fullurl = "";
			$args = array();
			
			for($i = 0; $i < count($links); $i++) {
				if($links[$i] != "")
				{
					$fullurl .= "/";
					if((substr($links[$i], -1) == "}") && (substr($links[$i], 0, 1) == "{")) {
						$args[] = substr($links[$i], 1, -1);
						$fullurl .= $links[$i];
					}
					else {
						$fullurl .= urlencode($links[$i]);
					}
				}
			}
			$route = new Route("PUT", $fullurl, $_action, $args);
			$this->routes[] = array($route);
			return $route;
		}
		else
		{
			$route = new Route("PUT", "/", $_action, array());
			$this->routes[] = array($route);
			return $route;
		}
	}
	
	public function delete($_url, $_action) {
		if($_url != "/")
		{
			$links = explode("/", $_url);
			
			$fullurl = "";
			$args = array();
			
			for($i = 0; $i < count($links); $i++) {
				if($links[$i] != "")
				{
					$fullurl .= "/";
					if((substr($links[$i], -1) == "}") && (substr($links[$i], 0, 1) == "{")) {
						$args[] = substr($links[$i], 1, -1);
						$fullurl .= $links[$i];
					}
					else {
						$fullurl .= urlencode($links[$i]);
					}
				}
			}
			$route = new Route("DELETE", $fullurl, $_action, $args);
			$this->routes[] = array($route);
			return $route;
		}
		else
		{
			$route = new Route("DELETE", "/", $_action, array());
			$this->routes[] = array($route);
			return $route;
		}
	}
	
	public function error404($_action) {
		if(is_callable($_action)) {
			$this->route404 = $_action;
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
	}
	
	public function baseURL() {
		return $this->_baseURL;
	}
	
	public function run() {
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		$path_elements = explode("/", $_SERVER['REQUEST_URI']);
		$tempPI = "";
		if (isset($path_elements[2])){
			for ($i = 2 ;$i < count($path_elements); $i++ )
				$tempPI .= "/".$path_elements[$i];
		}
		
		$requestUrl = $tempPI;
		
		$request = new Request();
		$response = new Response();
		
		$routing = null;
		
		$argsRoute = array();
		$args = null;
		
		foreach($this->routes as $route) {
			$argsRoute = null;
			$argsRoute = array();
			$fullurl = "";
			$lurl = $route[0]->getUrl();
			$links = explode("/", $lurl);
			$linksRequest = explode("/", $requestUrl);
			for($i = 0; $i < count($links); $i++) {
				if($lurl != "/") {
					if($links[$i] != "") {
						$fullurl .= "/";
						if((substr(urldecode($links[$i]), -1) == "}") && (substr(urldecode($links[$i]), 0, 1) == "{")) {
							if(isset($linksRequest[$i]))
							{
								$fullurl .= urlencode($linksRequest[$i]);
								$argsRoute[substr($links[$i], 1, -1)] = $linksRequest[$i];
							}
							else
							{
								$fullurl .= "&&&çç_@";
							}
						}
						else {
							$fullurl .= $links[$i];
						}
					}
				}
				else {
					$fullurl = "/";
				}
			}
			
			if(substr($fullurl, -1) == "/") {
				$fullurl = substr($fullurl, 0, count($fullurl));
			}
			
			if($fullurl === $requestUrl) {
				if($requestMethod == $route[0]->getMethod())
				{
					$routing = $route[0];
					$args = $argsRoute;
				}
			}
		}
		
		if($routing == null) {
			http_response_code(404);
			if($this->route404 != null) {
				$this->route404($request, $response, array());
			}
			else {
				throw new \Exception("404 Error - Not found");
			}
		}
		else {
			$middlewares = $routing->getMiddlewares();
			$action = $routing->getAction();
			if(count($middlewares) > 0) {
				for($i = 0; $i <= count($middlewares); $i++) {
					if(isset($middleware[$i + 1])) {
						$middleware[$i]($request, $response, $args, $middleware[$i + 1]);
					}
					else {
						$middleware[$i]($request, $response, $args, $action);
					}
				}
			}
			else
			{
				$action($request, $response, $args);
			}
		}
	}
}