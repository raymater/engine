<?php
session_start();

//require_once("../src/autoload.php");
require_once("vendor/autoload.php");

use \phpengine\Request as Request;
use \phpengine\Response as Response;

$config = array("debug" => true, "lang" => "en");
$app = new phpengine\Application($config);

$app->get("/", function(Request $req, Response $resp, $args, $app) {
	$resp->write("
		Hello World !
		<br/>
		<a href='".$app->getRoute("link2")->getFullUrl(array("mypage"))."'>Go to other page</a>
	");
})->setName("link1");

$app->get("/test/{foo}", function(Request $req, Response $resp, $args, $app) {
	$resp->write("
		An other route
		<br/>
		<a href='".$app->getRoute("link1")->getFullUrl()."'>Home page</a>
	");
})->setName("link2");

$app->run();