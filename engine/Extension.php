<?php
namespace engine;

class Extension
{
	protected $name;
	protected $routine;
	
	public function __construct($_name, $_routine) {
		$this->name = $_name;
		$this->routine = $_routine;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function start() {
		$routine();
	}
}