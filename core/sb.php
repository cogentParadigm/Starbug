<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/sb.php
 * The global sb object. provides data, errors, import/provide, load and pub/sub. The backbone of Starbug
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * The sb class. Provides data, errors, import/provide, pub/sub, and load to the rest of the application. The core component of StarbugPHP.
 * @ingroup core
 */
class sb {
	/**#@+
	* @access public
	*/
	/**
	 * @var db The db class is a PDO wrapper
	 */
	var $db;
	/**
	 * @var string holds the active scope (usually 'global' or a model name)
	 */
	var $active_scope = "global";
	/**
	 * @var array holds the utils that have been provided
	 */
	var $provided = array();
	/**
	 * @var array holds validation errors
	 */
	var $errors = array();
	/**
	 * @var array holds alerts
	 */
	var $alerts = array();
	/**#@-*/
	/**
	 * @var array holds mixed in objects
	 */
	var $imported = array();
	/**
	 * @var array holds function names of mixed in objects
	 */
	var $imported_functions = array();


	/**
	 * constructor. connects to db and sets default $_SESSION values
	 */
	function __construct() {
		set_exception_handler(array($this,'handle_exception'));
		set_error_handler(array($this,'handle_error'), error_reporting());
		register_shutdown_function(array($this, 'handle_shutdown')); 
		$this->db = new db('mysql:host='.Etc::DB_HOST.';dbname='.Etc::DB_NAME, Etc::DB_USERNAME, Etc::DB_PASSWORD, Etc::PREFIX);
		$this->db->set_debug(true);
	}

	/**
	 * publish a topic to any subscribers or hooks
	 * @param string $topic the topic name you would like to publish
	 * @param mixed $args any additional parameters will be passed in an array to the subscriber
	 */
	function publish($topic, $args=null) {
		global $request; global $sb;
		$args = func_get_args(); $topic = array_shift($args);
		if (false !== strpos($topic, ".")) {
			list($tags, $topic) = explode(".", $topic);
			$tags = array(array("slug" => $tags));
		} else $tags = (isset($request->tags)) ? $request->tags : array(array("slug" => "global"));
		foreach ($tags as $tag) {
			foreach (locate("$tag[slug].$topic.php", "hooks") as $hook) if (file_exists($hook)) include($hook);
		}
		return $args;
	}

	/**
	 * import function. only imports once when used with provide
	 * @param string $loc path of file to import without '.php' at the end
	 */
	function import($loc) {global $sb; $args = func_get_args(); foreach($args as $l) if (!isset($this->provided[$l])) include(BASE_DIR."/".$l.".php");}

	/**
	 * when imported use provide to prevent further imports from attempting to include it again
	 * @param string $loc the imported location. if i were to use $sb->import("util/form"), util/form.php would have $sb->provide("util/form") at the top
	 */
	function provide($loc) {$this->provided[$loc] = true;}

	/**
	 * get a model by name
	 * @param string $name the name of the model, such as 'users'
	 * @return the instantiated model
	 */
	function get($name) {
		return $this->db->model($name);
	}
	
	/**
	 * exception handler
	 */
	public function handle_exception($exception) {
		$this->import("core/lib/ErrorHandler");
		ErrorHandler::handle_exception($exception);
	}
	
	function handle_error($errno, $errstr, $errfile, $errline) {
		$this->import("core/lib/ErrorHandler");
		ErrorHandler::handle_error($errno, $errstr, $errfile, $errline);
	}
	
	function handle_shutdown() {
		if(is_null($e = error_get_last()) === false) {
			ob_end_flush();
		}
	}
	
	/**
	 * mixin an object to import its functions into this object
	 * @param object $object the object to mixin
	 */
	protected function mixin($object) {
		$new_import = new $object();
		$import_name = get_class($new_import);
		$import_functions = get_class_methods($new_import);
		array_push($this->imported, array($import_name, $new_import));
		foreach($import_functions as $key => $function_name) $this->imported_functions[$function_name] = &$new_import;
	}

	/**
	 * Handler for calls to non-existant functions. Allows calling of mixed in functions.
	 */
	public function __call($method, $args) {
		if(array_key_exists($method, $this->imported_functions)) {
			$args[] = $this;  
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}
?>
