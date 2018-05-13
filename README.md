# PHP-Engine

*A basic engine on PHP to manage and simplifie routes, HTTP requests and HTTP responses.*

## Get started

Make sure that you have 5 files : 
* Applicaiton.php
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