<?php
session_start();

require_once("engine/autoload.php");

use \engine\Request as Request;
use \engine\Response as Response;

$config = array("debug" => true);
$app = new engine\Application($config);

$app->get("/", function(Request $req, Response $resp, $args) {
	$resp->write("Hello World !");
});

$app->get("/test/{foo}", function(Request $req, Response $resp, $args) {
	$resp->write("An other route");
});

$app->run();