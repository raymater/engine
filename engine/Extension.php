<?php
namespace engine;

class Extension
{
	protected $name;
	protected $routine;
	protected $exec = null;
	
	public function __construct($_name, $_routine) {
		if(is_callable($_routine)) {
			$this->name = $_name;
			$this->routine = $_routine;
		}
		else
		{
			throw new \Exception("Incorrect routine. Type function expected.");
			http_response_code(500);
		}
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function start() {
		if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
			require __DIR__ . '/../vendor/autoload.php';
		}
		$this->exec = call_user_func($this->routine);
	}
	
	public function app() {
		if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
			require __DIR__ . '/../vendor/autoload.php';
		}
		return call_user_func($this->routine);
	}
}