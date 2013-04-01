<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/db/classes/db.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * @defgroup db
 * the db class
 * @ingroup db
 */
/**
 * The db class. loads DB driver and Model objects
 * @ingroup db
 */
class db {
	
	/**
	 * @var array holds instantiated models
	 */
	static $objects = array();

	/**
	 * check if a model exists
	 * @param string $name the name of the model
	 * @return bool true if model exists, false otherwise
	 */
	function has($name) {return ((self::$objects[$name]) || (file_exists(BASE_DIR."/var/models/".ucwords($name)."Model.php")));}	

	/**
	 * get a model by name
	 * @param string $name the name of the model, such as 'users'
	 * @return the instantiated model
	 */
	function model($name) {
		$class = ucwords($name);
		if (!isset(self::$objects[$name])) {
			if (file_exists(BASE_DIR."/var/models/".$class."Model.php")) {
				//include the base model
				include(BASE_DIR."/var/models/".$class."Model.php");
				$last = $class."Model";
				
				//get additional models
				$models = locate("$class.php", "models");
				$count = count($models);
				$search = "class $class {";
				
				//loop through found models
				for ($i = 0; $i < $count; $i++) {
					//get file contents
					$contents = file_get_contents($models[$i]);
					//make class name unique and extend the previous class
					$class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/models/', $models[$i])))."__$class";
					$replace = "class $class extends $last {";
					//replace and eval
					eval('?>'.str_replace($search, $replace, $contents));
					//set $last for the next round
					$last = $class;
				}
				
				//return the base model if no others
				if ($count == 0) $class .= "Model";
				
			} else $class = "Table"; //return the base table if the model does not exist
			
			//instantiate save the object
			self::$objects[$name] = new $class($this, $name);
		}
		
		//return the saved object
		return self::$objects[$name];
	}

}
?>
