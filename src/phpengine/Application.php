<?php
namespace phpengine;

class Application
{
	protected $debug = false;
	protected $lang = "en";
	protected $routes = array();
	protected $route404 = null;
	protected $_baseURL = null;
	protected $PHPversion = null;
	protected $serverSoftware = null;
	protected $globalVars = array();
	protected $timezone = "GMT";
	
	/**
		* Create the main application
		*
		* Make the configuration for your app by config array passed on param. Also set the base path for URL, the PHP version and the name of the current server software.
		*
		* @param array $_config
		*	The config array : support "debug", "lang" and "timezone".
		* @return void
	**/
	public function __construct($_config = array()) {
		$this->routes = array();
		
		if(array_key_exists("debug", $_config)) {
			if($_config["debug"] == true) {
				$this->debug = true;
			}
		}
		
		if(array_key_exists("lang", $_config)) {
			$this->lang = $_config["lang"];
		}
		
		if(array_key_exists("timezone", $_config)) {
			$this->timezone = $_config["timezone"];
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
		
		$this->PHPversion = phpversion();
		
		$this->serverSoftware = $_SERVER['SERVER_SOFTWARE'];
		
		date_default_timezone_set($this->timezone);
    }
	
	/**
		* Create a custom route
		*
		* Make a route by specifying URL, the action and the method.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param string $_method
		*	Your HTTP Method (Default : "GET")
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function route($_url, $_action, $_method = "GET", $_auth = null) {
		$route = null;
		if($_url != "/")
		{
			$route = new Route($_method, $_url, $_action, $this, $_auth);
		}
		else
		{
			$route = new Route($_method, "/", $_action, $this, $_auth);
		}
		
		$this->routes[] = $route;
		
		$methodsAllowed = array();
		foreach($this->routes as $aRoute) {
			if($aRoute->getUrl() == $_url) {
				$methodsAllowed[] = $aRoute->getMethod();
			}
		}
		
		foreach($this->routes as $aRoute) {
			if($aRoute->getUrl() == $_url) {
				$aRoute->allowMethods = $methodsAllowed;
			}
		}
		
		return $route;
	}
	
	/**
		* Create a GET route
		*
		* Make a route with GET method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function get($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "GET", $_auth);
	}
	
	/**
		* Create a POST route
		*
		* Make a route with POST method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function post($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "POST", $_auth);
	}
	
	/**
		* Create a PUT route
		*
		* Make a route with PUT method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function put($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "PUT", $_auth);
	}
	
	/**
		* Create a DELETE route
		*
		* Make a route with DELETE method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function delete($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "DELETE", $_auth);
	}
	
	/**
		* Create a PATCH route
		*
		* Make a route with PATCH method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function patch($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "PATCH", $_auth);
	}
	
	/**
		* Create a OPTIONS route
		*
		* Make a route with OPTIONS method while specifying URL and the action.
		*
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Set if route require HTTP Auth (Default : null)
		* @return Route
		*	The Route object created.
	**/
	public function options($_url, $_action, $_auth = null) {
		return $this->route($_url, $_action, "OPTIONS", $_auth);
	}
	
	/**
		* Set a 404 default route for the app
		*
		* Set action to do when a ressource is not found.
		*
		* @param callable $_action
		*	What do you want to do ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @return void
	**/
	public function error404($_action) {
		if(is_callable($_action)) {
			$this->route404 = $_action;
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
	}
	
	/**
		* Get the base URL
		*
		* Return the base URL/path for your app.
		*
		* @return string
		*	The base path
	**/
	public function baseURL() {
		return $this->_baseURL;
	}
	
	/**
		* Set a global var
		*
		* Put or replace a value accessible on the entire application.
		*
		* @param string $_name
		*	Name of your global var
		* @param mixed $_value
		*	A value to save
		* @return mixed
		*	The value saved ($_value)
	**/
	public function setVar($_name, $_value) {
		$this->globalVars[$_name] = $_value;
		return $this->globalVars[$_name];
	}
	
	/**
		* Delete a global var by name
		*
		* Delete a value accessible on the entire application.
		*
		* @param string $_varName
		*	Name of your global var
		* @return bool
		*	Return true if the method has found a the var, false if not.
	**/
	public function deleteVar($_varName) {
		$resp = false;
		foreach ($this->globalVars as $key => $value) {
			if($key === $_varName) {
				unset($array[$key]);
				$resp = true;
			}
		}
		return $resp;
	}
	
	/**
		* Get global var
		*
		* Get a global var by name or get all global vars (set $_varName to null).
		*
		* @param string $_varName
		*	Name of your global var. If is null (by default) = return all global vars.
		* @return mixed
		*	Return the value of the global var or an associative array representing all global vars.
	**/
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
	
	/**
		* Get the debug setting
		*
		* Return a boolean value representing if the app is on debug mode or not.
		*
		* @return bool
		*	Return true if is app is on debug mode, false if not.
	**/
	public function isDebug() {
		return $this->debug;
	}
	
	/**
		* Get the PHP version
		*
		* Return the current PHP version running on the server.
		*
		* @return string
		*	Return the PHP Version.
	**/
	public function getPhpVersion() {
		return $this->PHPversion;
	}
	
	/**
		* Get the name of the server software
		*
		* Return the name of the server software
		*
		* @return string
		*	Return the name of the server software.
	**/
	public function getServerSoftware() {
		return $this->serverSoftware;
	}
	
	/**
		* Get the default timezone
		*
		* Return the default timezone identifier defined on the app (GMT by default).
		*
		* @return string
		*	Return the name of the timezone identifier.
	**/
	public function getTimezone() {
		return $this->timezone;
	}
	
	/**
		* Set the default timezone
		*
		* Change the timezone identifier for the app.
		*
		* @param string $_timezone
		*	Name of your timezone identifier (by default : GMT).
		* @return string
		*	Return the name of the current timezone identifier.
	**/
	public function setTimezone($_timezone = "GMT") {
		$this->timezone = $_timezone;
		$res = date_default_timezone_set($this->timezone);
		date_timezone_set($this->timezone);
		if($res == false) {
			$this->timezone = "GMT";
			date_default_timezone_set($this->timezone);
			date_timezone_set($this->timezone);
		}
		return $this->timezone;
	}
	
	/**
		* Get route
		*
		* Return a specific route by its name or all routes available (null).
		*
		* @param string $_name
		*	Name of your route (by default : null).
		* @return mixed
		*	Return the Route object of your route, or null if is not found or an array of Route object if $_name param is null
	**/
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
	
	/**
		* Get the language
		*
		* Return the language of the app (used on Content-Language HTTP header) defined by RFC 5646. "en" by default or defined language on config app.
		*
		* @return string
		*	Return the language (RFC 5646).
	**/
	public function getLang() {
		return $this->lang;
	}
	
	/**
		* Run the application
		*
		* Running the application to match the current URL route with all defined route and execute middlewares and the Route function defined. If Route is not found, run the 404 route action.
		*
		* @return Application
		*	Return the Application object.
	**/
	public function run() {
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
		
		$errorMethod = false;
		$passedControl = false;
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
					$errorMethod = false;
					$passedControl = true;
				}
				else
				{
					$errorMethod = true;
				}
			}
		}
		
		if($errorMethod === true && $passedControl === false) {
			if(count($route->allowMethods) > 0) {
				$stringMethods = "";
				foreach($route->allowMethods as $aMethod) {
					$stringMethods .= $aMethod.", ";
				}
				if(substr($stringMethods, -1) == " ") {
					$stringMethods = substr($stringMethods, 0, -1);
				}
				if(substr($stringMethods, -1) == ",") {
					$stringMethods = substr($stringMethods, 0, -1);
				}
				header("Allow: ".$stringMethods);
			}
			
			http_response_code(405);
			throw new \Exception("405 Error - Method Not Allowed");
			exit;
		}
		else
		{
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
				if(count($routing->allowMethods) > 0) {
					$stringMethods = "";
					foreach($routing->allowMethods as $aMethod) {
						$stringMethods .= $aMethod.", ";
					}
					if(substr($stringMethods, -1) == " ") {
						$stringMethods = substr($stringMethods, 0, -1);
					}
					if(substr($stringMethods, -1) == ",") {
						$stringMethods = substr($stringMethods, 0, -1);
					}
					header("Allow: ".$stringMethods);
				}
				
				$middlewares = $routing->getMiddlewares();
				$action = $routing->getAction();
				$continue = true;
				if(count($middlewares) > 0) {
					for($i = 0; $i < count($middlewares); $i++) {
						if($continue == true) {
							$continue = $middlewares[$i]($request, $response, $args, $this);
						}
					}
				}
				
				if($continue == true) {
					if($routing->isAuth() === true) {
						if($routing->getAuth()->getType() == "BASIC") {
							if(!isset($_SERVER['PHP_AUTH_USER'])) {
								http_response_code(401);
								header('WWW-Authenticate: Basic realm="'.$routing->getAuth()->getRealm().'"');
								header('HTTP/1.1 401 Unauthorized');
								if($routing->getAuth()->getActionNoAuth() === null) {
									echo '401 - Authentication required';
									exit;
								}
								else {
									$actionNoAuth = $routing->getAuth()->getActionNoAuth();
									$actionNoAuth($request, $response, $args, $this);
								}
							}
							else {
								$credentials = $routing->getAuth()->getCredentials();
								$authenticated = false;
								
								foreach($credentials as $user => $password) {
									if($user === $_SERVER['PHP_AUTH_USER'] && $password === $_SERVER['PHP_AUTH_PW']) {
										$authenticated = true;
									}
								}
								
								if($authenticated === true) {
									$action($request, $response, $args, $this);
								}
								else {
									$actionNoAuth = $routing->getAuth()->getActionNoAuth();
									$actionNoAuth($request, $response, $args, $this);
								}
							}
						}
						else {
							if($routing->getAuth()->getType() == "DIGEST") {
								function http_digest_parse($txt)
								{
									$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
									$data = array();
									$keys = implode('|', array_keys($needed_parts));
								 
									preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

									foreach ($matches as $m) {
										$data[$m[1]] = $m[3] ? $m[3] : $m[4];
										unset($needed_parts[$m[1]]);
									}

									return $needed_parts ? false : $data;
								}
								
								if(empty($_SERVER['PHP_AUTH_DIGEST'])) {
									http_response_code(401);
									header('HTTP/1.1 401 Unauthorized');
									header('WWW-Authenticate: Digest realm="'.$routing->getAuth()->getRealm().'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($routing->getAuth()->getRealm()).'"');
									
									if($routing->getAuth()->getActionNoAuth() === null) {
										echo '401 - Authentication required';
										exit;
									}
									else {
										$actionNoAuth = $routing->getAuth()->getActionNoAuth();
										$actionNoAuth($request, $response, $args, $this);
									}
								}
								else {
									$data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
									
									$credentials = $routing->getAuth()->getCredentials();
									
									if($data === false || !isset($credentials[$data['username']])) {
										if($routing->getAuth()->getActionNoAuth() === null) {
											echo '401 - Authentication required';
											exit;
										}
										else {
											$actionNoAuth = $routing->getAuth()->getActionNoAuth();
											$actionNoAuth($request, $response, $args, $this);
										}
									}
									else {
										$A1 = md5($data['username'] . ':' . $routing->getAuth()->getRealm() . ':' . $credentials[$data['username']]);
										$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
										$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

										if ($data['response'] != $valid_response) {
											if($routing->getAuth()->getActionNoAuth() === null) {
												echo '401 - Authentication required';
												exit;
											}
											else {
												$actionNoAuth = $routing->getAuth()->getActionNoAuth();
												$actionNoAuth($request, $response, $args, $this);
											}
										}
										else {
											$action($request, $response, $args, $this);
										}
									}
								}
							}
							else {
								throw new \Exception("Authentication type is not valid");
								http_response_code(500);
							}
						}
					}
					else {
						$action($request, $response, $args, $this);
					}
				}
			}
		}
		
		return $this;
	}
}