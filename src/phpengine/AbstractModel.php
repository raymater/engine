<?php
namespace phpengine;

abstract class AbstractModel {
	protected $fields = array();
	protected static $connection = null;
	
	/**
		* Create a model object
		*
		* Make a model object and connecting to database
		*
		* @return void
	**/
	public function __construct() {
		
	}
	
	/**
		* Save all datas on database
		*
		* Make an INSERT INTO or an UDPDATE query.
		*
		* @return Model
		*	The Model itself
	**/
	public function save() {
		$ids = static::getPrimaryKey();
		$update = true;
		foreach($ids as $id) {
			if(!isset($this->fields[$id])) {
				$update = false;
			}
			else {
				if($this->$id === null) {
					$update = false;
				}
			}
		}
		
		if($update == true) {
			$query = "UPDATE `".get_called_class()."` SET ";
			
			$sep = false;
			foreach($this->fields as $f => $val) {
				if(!is_int($f)) {
					if($sep == true) {
						$query .= ", ";
					}
					$query .= "`".$f."` = ";
					
					if(is_int($val) || is_double($val)) {
						$query .= $val;
					}
					else {
						$query .= "'".addslashes($val)."'";
					}
					
					$sep = true;
				}
			}
			
			$query .= " WHERE ";
			
			$and = false;
			$ids = static::getPrimaryKey();
			foreach($ids as $id) {
				if($and == true) {
					$query .= " AND ";
				}
				$query .= "`".$id."` = ";
				$value = $this->fields[$id];
				if(is_int($value) || is_double($value)) {
					$query .= $value;
				}
				else {
					$query .= "'".addslashes($value)."'";
				}
				$and = true;
			}
			
			static::$connection->query($query);
		}
		else {
			$query = "INSERT INTO `".get_called_class()."` (";
			$f = array_keys($this->fields);
			
			$i = 1;
			foreach($f as $field) {
				if(!is_int($field)) {
					$query .= "`".$field."`";
					
					if(count($f) != $i) {
						$query .= ", ";
					}
				}
				
				$i++;
			}
			
			$query .= ") VALUES (";
			
			$i = 1;
			foreach($this->fields as $val) {
				if(is_int($val) || is_double($val)) {
					$query .= $val;
				}
				else {
					$query .= "'".addslashes($val)."'";
				}
				
				if(count($f) != $i) {
					$query .= ", ";
				}
					
				$i++;
			}
			
			$query .= ")";
			static::$connection->query($query);
			
			$pri = static::getPrimaryKey();
			if(count($pri) == 1) {
				$priF = $pri[0];
				$this->priF = static::$connection->lastInsertId();
			}
		}
		
		return $this;
	}
	
	/**
		* Delete the object on database
		*
		* Make a DELETE FROM query.
		*
		* @return Model
		*	The Model itself
	**/
	public function delete() {
		$ids = static::getPrimaryKey();
		$query = "DELETE FROM `".get_called_class()."` WHERE ";
		
		$and = false;
		foreach($ids as $id) {
			if($and == true) {
				$query .= " AND ";
			}
			$query .= "`".$id."` = ";
			$value = $this->fields[$id];
			if(is_int($value) || is_double($value)) {
				$query .= $value;
			}
			else {
				$query .= "'".addslashes($value)."'";
			}
			$and = true;
		}
		
		static::$connection->query($query);
		return $this;
	}
	
	/**
		* Get a model object by field or by ID(s)
		*
		* Make a SELECT query. Search by field or by ID(s). If you have many primary keys, you have to pass an associative array like this :
		*	[ "my_first_field" => "value", "my_second_field" => "value", ...]
		* If $_field param is null, equivalent as all() method.
		*
		* @param mixed $_field (Default : null)
		* 	ID(s) value(s)
		* @return mixed
		*	Model instance or array of models
	**/
	public static function get($_field = null) {
		if($_field == null || $_field == array()) {
			return static::all();
		}
		else
		{
			$query = "SELECT * FROM `".get_called_class()."` WHERE ";
			if(is_array($_field)) {
				$and = false;
				foreach($_field as $field => $value) {
					if($and == true) {
						$query .= " AND ";
					}
					$query .= "`".$field."` = ";
					if(is_int($value) || is_double($value)) {
						$query .= $value;
					}
					else {
						$query .= "'".addslashes($value)."'";
					}
					$and = true;
				}
			}
			else {
				if(count(static::getPrimaryKey()) == 1) {
					$query .= static::getPrimaryKey()[0]." = ";
					if(is_int($_field) || is_double($_field)) {
						$query .= $_field;
					}
					else {
						$query .= "'".addslashes($_field)."'";
					}
				}
				else {
					throw new \Exception("You don't have only one primary key on your table ".get_called_class());
					http_response_code(500);
					exit;
				}
			}
			
			$q = static::$connection->query($query);
			$tab = $q->fetch();
			
			$nameObj = get_called_class();
			$o = new $nameObj();
			foreach($tab as $f => $val) {
				if(!is_int($f)) {
					$o->$f = utf8_encode($val);
				}
			}
			
			return $o;
		}
	}
	
	/**
		* Get all fields name
		*
		* Return all fields name on your table.
		*
		* @return array
		*	Array with all fields names
	**/
	public static function getAttributes() {
		$q = static::$connection->query("DESCRIBE `".get_called_class()."`");
		$tab = $q->fetchAll();
		
		$fields = array();
		foreach($tab as $res) {
			$fields[] = $res["Field"];
		}
		
		return $fields;
	}
	
	/**
		* Get primary key fields
		*
		* Return all primary key fields name on your table.
		*
		* @return array
		*	Array with all primary key fields names
	**/
	public static function getPrimaryKey() {
		$q = static::$connection->query("DESCRIBE `".get_called_class()."`");
		$tab = $q->fetchAll();
		
		$fields = array();
		foreach($tab as $res) {
			if($res["Key"] == "PRI") {
				$fields[] = $res["Field"];
			}
		}
		
		return $fields;
	}
	
	/**
		* Get the number of records
		*
		* Make the query SELECT COUNT(*) FROM model_name
		*
		* @return integer
		*	Number of records
	**/
	public static function count() {
		$q = static::$connection->query("SELECT COUNT(*) AS nb FROM `".get_called_class()."`");
		$tab = $q->fetch();
		
		return $tab["nb"];
	}
	
	/**
		* Get all records
		*
		* Make the query SELECT * FROM model_name
		*
		* @return array
		*	Array of Model objects
	**/
	public static function all() {
		$q = static::$connection->query("SELECT * FROM `".get_called_class()."`");
		$tab = $q->fetchAll();
		$models = array();
		$primaryKeys = static::getPrimaryKey();
		
		foreach($tab as $rec) {
			$ids = array();
			$fields = array_keys($rec);
			foreach($fields as $f) {
				if(in_array($f, $primaryKeys)) {
					if(!is_int($f)) {
						$ids[$f] = $rec[$f];
					}
				}
			}
			$object = static::get($ids);
			$models[] = $object;
		}
		
		return $models;
	}
	
	/**
		* Set the connection to database
		*
		* Set the connection to database with a PDO object
		*
		* @param \PDO $_connection
		* 	PDO object
		* @return void
	**/
	public static function setConnection($_connection) {
		static::$connection = $_connection;
	}
	
	/**
		* Get a JSON representation for your Model object
		*
		* Convert Model object to a JSON string
		*
		* @return string
		*	JSON string
	**/
	public function toJSON()
	{
		return json_encode($this->fields, JSON_UNESCAPED_UNICODE);
	}
	
	/**
		* Get a value for a specific fields
		*
		* Return a field value
		*
		* @return string
		*	Value of field
	**/
	public function __get($_name) {
		return $this->fields[$_name];
	}
	
	/**
		* Set a value for a specific fields
		*
		* Modify a value for a specific field
		*
		* @return Model
		*	The Model itself
	**/
	public function __set($_name, $_value) {
		$this->fields[$_name] = $_value;
		return $this;
	}
}