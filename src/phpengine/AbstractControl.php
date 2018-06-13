<?php
namespace phpengine;

use \phpengine\Request as Request;
use \phpengine\Response as Response;

abstract class AbstractControl {
	protected $app = null;
	
	/**
		* Create a controller object
		*
		* Make a controller object
		*
		* @param Application $_app
		*	Phpengine Application context
		* @return void
	**/
	public function __construct($_app) {
		$this->app = $_app;
	}
}