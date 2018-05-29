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
	protected $auth;
	protected $middlewares = array();
	
	/**
		* Create a Route object
		*
		* Make a Route object describing the name, URL, action to do and middlewares.
		*
		* @param string $_method
		*	HTTP Method (GET, POST, PUT, PATCH, DELETE, OPTIONS)
		* @param string $_url
		*	Your URL (start with "/").
		* @param callable $_action
		*	What do you want to do on this URL ? (function(Request $req, Response $resp, $args, $app) { // Do Something }).
		* @param bool $_auth
		*	Indicate if route require a Basic HTTP Authentication (by default : false)
		* @param Application $_app
		*	The current application
		* @return void
	**/
	public function __construct($_method, $_url, $_action, $_app, $_auth = false) {
		if(is_callable($_action)) {
			$args = array();
			$links = explode("/", $_url);
			$fullurl = "";
			
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
			
			$this->method = $_method;
			$this->url = $_url;
			$this->action = $_action;
			$this->args = $args;
			$this->app = $_app;
			$this->auth = $_auth;
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
    }
	
	/**
		* Get the HTTP method
		*
		* Return the route method.
		*
		* @return string
		*	HTTP method
	**/
	public function getMethod() {
		return $this->method;
	}
	
	/**
		* Get the full URL of route
		*
		* Return the full link to the route. Replace args on URL by your args array.
		*
		* @param array $_args
		*	Array of args (by default : array())
		* @return string
		*	The full URL
	**/
	public function getFullUrl($_args = array()) {
		$links = explode("/", $this->url);
		if((count($_args) == 0) && (count($this->args) == 0))
		{
			return $this->app->baseURL().$this->url;
		}
		else
		{
			if(count($_args) == count($this->args)) {
				$thisurl = $this->app->baseURL();
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
			else {
				throw new \Exception("Incorrect args array. Your args array has ".count($_args)." elements. Expected ".count($links)." elements");
				http_response_code(500);
			}
		}
	}
	
	/**
		* Get the URL
		*
		* Return the route URL.
		*
		* @return string
		*	URL
	**/
	public function getUrl() {
		return $this->url;
	}
	
	/**
		* Get the action
		*
		* Return the route action.
		*
		* @return callable
		*	Action
	**/
	public function getAction() {
		return $this->action;
	}
	
	/**
		* Get arguments
		*
		* Return an array of arguments.
		*
		* @return array
		*	Arguments
	**/
	public function getArgs() {
		return $this->args;
	}
	
	/**
		* Get name
		*
		* Return the name of the route
		*
		* @return string
		*	Name
	**/
	public function getName() {
		return $this->name;
	}
	
	/**
		* Get middlewares
		*
		* Return an array of middlewares (callables).
		*
		* @return array
		*	Middlewares
	**/
	public function getMiddlewares() {
		return $this->middlewares;
	}
	
	/**
		* Set name of route
		*
		* Set name for your route.
		*
		* @param string $_name
		*	Name of your route (by default : null)
		* @return Route
		*	The object Route
	**/
	public function setName($_name = null) {
		$this->name = $_name;
		return $this;
	}
	
	/**
		* Is this route require HTTP authentication ?
		*
		* Return a boolean value represent if this route require HTTP authentication.
		*
		* @return bool
		*	Authentication setting
	**/
	public function isAuth() {
		return $this->auth;
	}
	
	/**
		* Add a middleware
		*
		* Put a middleware action.
		*
		* @param callable $_action
		*	What do you want to do before the route action ?
		* @return Route
		*	The object Route
	**/
	public function add($_action) {
		if(is_callable($_action)) {
			$this->middlewares[] = $_action;
			return $this;
		}
		else {
			throw new \Exception("Incorrect action. Type function expected.");
			http_response_code(500);
		}
		return $this;
	}
}