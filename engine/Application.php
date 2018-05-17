<?php
namespace engine;

class Application
{
	protected $debug = false;
	protected $lang = "en";
	protected $routes = array();
	protected $route404 = null;
	protected $_baseURL = null;
	protected $globalVars = array();
	protected $extensions = array();
	
	public function __construct($_config) {
		$this->routes = array();
		
		if(array_key_exists("debug", $_config)) {
			if($_config["debug"] == true) {
				$this->debug = true;
			}
		}
		
		if(array_key_exists("lang", $_config)) {
			$this->lang = $_config["lang"];
		}
		
		if(array_key_exists("no-extension", $_config)) {
			if("no-extension" === true) {
				$this->extensions = array();
			}
			else {
				$this->extensions = null;
			}
		}
		
		if($this->debug == true) {
			error_reporting(E_ALL);
		}
		else {
			error_reporting(0);
		}
		
		$protocol = "http";
		if(isset($_SERVER["REQUEST_SCHEME"])) {
			$protocol = $_SERVER["REQUEST_SCHEME"];
		}
		
		$currentPath = $_SERVER['PHP_SELF'];
		$pathInfo = pathinfo($currentPath);
		
		$this->_baseURL = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER['HTTP_HOST'].$pathInfo['dirname'];
		
		if (!(function_exists('password_hash'))) {
			require_once("lib/password_hash.php");
		}
    }
	
	protected function route($_url, $_action, $_method) {
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
			$route = new Route($_method, $fullurl, $_action, $args, $this);
			$this->routes[] = $route;
			return $route;
		}
		else
		{
			$route = new Route($_method, "/", $_action, array(), $this);
			$this->routes[] = $route;
			return $route;
		}
	}
	
	public function get($_url, $_action) {
		return $this->route($_url, $_action, "GET");
	}
	
	public function post($_url, $_action) {
		return $this->route($_url, $_action, "POST");
	}
	
	public function put($_url, $_action) {
		return $this->route($_url, $_action, "PUT");
	}
	
	public function delete($_url, $_action) {
		return $this->route($_url, $_action, "DELETE");
	}
	
	public function patch($_url, $_action) {
		return $this->route($_url, $_action, "PATCH");
	}
	
	public function options($_url, $_action) {
		return $this->route($_url, $_action, "OPTIONS");
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
	
	public function setVar($_name, $_value) {
		$this->globalVars[$_name] = $_value;
	}
	
	public function deleteVar($_varName) {
		foreach ($this->globalVars as $key => $value) {
			if($key === $_varName) {
				unset($array[$key]);
			}
		}
	}
	
	public function getVar($_varName = null) {
		if($_varName != null) {
			$valueVar = null;
			foreach ($this->globalVars as $key => $value) {
				if($key === $_varName) {
					$valueVar = $value;
				}
			}
			return $valueVar;
		}
		else {
			return $this->globalVars;
		}
	}
	
	public function isDebug() {
		return $this->debug;
	}
	
	public function getRoute($_name = null) {
		if($_name != null) {
			$thisRoute = null;
			foreach ($this->routes as $route) {
				if($route->getName() === $_name) {
					$thisRoute = $route;
				}
			}
			return $thisRoute;
		}
		else
		{
			return $this->routes;
		}
	}
	
	public function getLang() {
		return $this->lang;
	}
	
	public function getExtension($_name = null) {
		if($this->extensions !== null) {
			if($_name === null) {
				return $this->extensions;
			}
			else {
				$thisExt = null;
				foreach ($this->extensions as $ext) {
					if($ext->getName() === $_name) {
						$thisExt = $ext;
					}
				}
				return $thisExt;
			}
		}
		else {
			throw new \Exception("Extensions are disabled.");
			http_response_code(500);
		}
	}
	
	public function addExtension($_name, $_routine) {
		if($this->extensions !== null) {
			$ext = new Extension($_name, $_routine);
			
			$this->extensions[] = $ext;
		}
		else {
			throw new \Exception("Extensions are disabled.");
			http_response_code(500);
		}
		return $this;
	}
	
	public function run() {
		if($this->extensions != null) {
			foreach ($this->extensions as $ext) {
				$ext->start();
			}
		}
		
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		$path_elements = $_SERVER['REQUEST_URI'];
		
		$string_path = $path_elements;
		
		$currentPath = $_SERVER['PHP_SELF'];
		$pathInfo = pathinfo($currentPath);
		
		$string_path = str_replace($pathInfo["dirname"], '', $string_path);
		
		if(substr($string_path, -1) == "/" && $string_path != "/") {
			$string_path = substr($string_path, 0, count($string_path) - 2);
		}
		
		$path_elements = explode("/", $string_path);
		
		$requestUrl = $string_path;
		
		$request = new Request($this);
		$response = new Response($this);
		
		$routing = null;
		
		$argsRoute = array();
		$args = null;
		
		foreach($this->routes as $route) {
			$argsRoute = null;
			$argsRoute = array();
			$fullurl = "";
			$lurl = $route->getUrl();
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
				if($requestMethod == $route->getMethod())
				{
					$routing = $route;
					$args = $argsRoute;
				}
			}
		}
		
		if($routing == null) {
			http_response_code(404);
			if($this->route404 != null) {
				$this->route404($request, $response, array(), $this);
			}
			else {
				throw new \Exception("404 Error - Not found");
			}
		}
		else {
			$middlewares = $routing->getMiddlewares();
			$action = $routing->getAction();
			if(count($middlewares) > 0) {
				$continue = true;
				for($i = 0; $i < count($middlewares); $i++) {
					if($continue == true) {
						$continue = $middlewares[$i]($request, $response, $args, $this);
					}
				}
				if($continue == true) {
					$action($request, $response, $args, $this);
				}
			}
			else
			{
				$action($request, $response, $args, $this);
			}
		}
		
		return $this;
	}
}