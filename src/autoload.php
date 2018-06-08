<?php
function load($class) {
	if(!empty($class)) {
		$class_path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
		$class_tab = explode(DIRECTORY_SEPARATOR, $class_path);
		for($i=0; $i<count($class_tab);$i++) {
			if(!isset($class_tab[$i]))
				unset($class_tab[$i]);
        }
		$class_path = implode(DIRECTORY_SEPARATOR, $class_tab);
		if(file_exists("..".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR.$class_path.".php")) {
			require_once("..".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR.$class_path.".php");
			return true;
        }
    }
    echo 'The file containing the class '.$class.' was not found !';
    exit;
}

spl_autoload_register('load');