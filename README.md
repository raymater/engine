# PHP-Engine

*A basic engine on PHP to manage and simplify routes, HTTP requests and HTTP responses.*

## Get started

Make sure that you have 5 files : 
* Application.php
* autoload.php
* Request.php
* Response.php
* Route.php

Put these files on *engine* repository.

Create an *index.php* file.


**A basic *index.php* file :**
```
<?php
session_start();

require_once("engine/autoload.php");

use \engine\Request as Request;
use \engine\Response as Response;

// Settings
$config = array("debug" => true, "lang" => "fr");
$app = new engine\Application($config);

// Declare your first route : my home
$app->get("/", function(Request $req, Response $resp, $args, $app) {
	$resp->write("
		Hello World !
	");
})->setName("my home");

$app->run();
```


**Declare each route as follows that :**
```
$app->HTTPmethod("/your_path", function(Request $req, Response $resp, $args, $app) {
	// Do something...
})->setName("your route name");
```

**Function parameters : $req, $resp, $args, $app :**
* **$req** : represent an instance of the HTTP request
* **$resp** : represent an instance of the HTTP responses
* **$args** : associative array of your args on URL
* **$app** : represent an instance of the current app


**HTTP Methods :**
* GET : ``$app->get`` method
* POST : ``$app->post`` method
* PUT : ``$app->put`` method
* DELETE : ``$app->delete`` method


**Args :**
You can declare a route like this :
```
$app->HTTPmethod("/your_path/{first_arg}/{second_arg}", function(Request $req, Response $resp, $args, $app) {
	// Do something...
})->setName("your route name");
```

In this example, there are 2 args : "first_arg" and "second_arg". If you go to */your_path/foo/bar* URL, ``$args["first_arg"] = foo`` and ``$args["second_arg"] = bar``.


**Debug mode :**
If you set debug mode on app config (``$config = array("debug" => true);``), PHP error display is activate and cache control is desactivate. By default, debug isn't activate.
You can check if debug mode is active with ``$app->isDebug()`` (return a boolean value) method.


**Middlewares :**
Middleware is a function executed before the main function of a route. You can delare a middleware with ``add`` method for a Route object like this :
```
$app->get("/", function(Request $req, Response $resp, $args, $app) {
	// Do something... : this is the main function
})->setName("link1")->add(function(Request $req, Response $resp, $args, $app) {
	// Do something...
	
	// return true;    => Call next action (here : the main action)...
	// return false;   => Stop action
});
```

You have to return a boolean value for each middleware. You can also declare multiple middlewares like this :
```
$app->get("/", function(Request $req, Response $resp, $args, $app) {
	// Do something... : this is the main function
})->setName("link1")->add(function(Request $req, Response $resp, $args, $app) {
	// Do something...
	
	// return true;    => Call next middleware...
	// return false;   => Stop action
})->add(function(Request $req, Response $resp, $args, $app) {
	// Do something...
	
	// return true;    => Call the main function...
	// return false;   => Stop action
});
```

If you want to declare your middleware on an other file you can make a php file like this (myMiddleware.php) :
```
<?php
namespace middleware; // Put your php file on middleware directory (or your namespace name)
use \engine\Request as Request;
use \engine\Response as Response;
class myMiddleware {
    public function __invoke(Request $req, Response $resp, $args, $app)
    {
        // Do something...
		
		// return true/false;
    }
}
```
And add your middleware to route like this on *index.php* :
```
$app->get("/", function(Request $req, Response $resp, $args, $app) {
	// Do something... : this is the main function
})->setName("link1")->add(new \middleware\myMiddleware());
```


**404 page :**
To make a custom 404 page you have to declare a special route for 404 like this on index.php :
```
$app->error404(function(Request $req, Response $resp, $args, $app) {
	// Do something...
});
```


**Global vars :**
You can declare a global var to access a value on the entire app like this :
```
$app->setVar("var", "abcdef");
echo $app->getVar("var"); // abcdef
```


**Get the full URL for a route :**
```
$app->getRoute("name of your route")->getFullUrl(); // return a string for the complete URL like "http://host/base_folder/path"
```

Make a custom URL by binding an array representing args value for a route. Example for route */your_path/{first_arg}/{second_arg}* named "myRoute" :
```
$app->getRoute("myRoute")->getFullUrl(array("foo","bar")); // return a string for the complete URL : "http://host/base_folder/your_path/foo/bar"
```