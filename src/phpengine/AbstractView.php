<?php
namespace phpengine;

use \phpengine\Request as Request;
use \phpengine\Response as Response;

abstract class AbstractView {
	protected $app = null;
	protected $data;
	
	/**
		* Create a view object
		*
		* Make a view object
		*
		* @param Application $_app
		*	Phpengine Application context
		* @param array $_data
		*	Array contains all datas sent (Default : array()).
		* @return void
	**/
	public function __construct($_app, $_data = array()) {
		$this->app = $_app;
		$this->data = $_data;
	}
}