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
	 * @var array active user
	 */
	var $user = false;
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
	/**
	 * @var array holds $db change listeners
	 */
	var $listeners = array();
	/**#@-*/
	/**
	 * @var array holds mixed in objects
	 */
	var $imported = array();
	/**
	 * @var array holds function names of mixed in objects
	 */
	var $imported_functions = array();
	
	static $instance;


	/**
	 * constructor. connects to db and starts the session
	 */
	function __construct($db) {
		self::$instance = $this;
		set_exception_handler(array($this,'handle_exception'));
		set_error_handler(array($this,'handle_error'), error_reporting());
		register_shutdown_function(array($this, 'handle_shutdown'));
		$this->db = $db;
		if (defined("Etc::DEBUG")) $this->db->set_debug(Etc::DEBUG);
		$this->start_session();
	}
	
	function set_database($db) {
		$this->db = $db;
		if (defined("Etc::DEBUG")) $this->db->set_debug(Etc::DEBUG);
		foreach ($this->listeners as $object) $object->set_database($db);
	}
	
	/**
	 * load the Session class and validate the current session if the user has a cookie
	 */
	function start_session() {
		$this->import("core/lib/Session");
		if (false !== ($session = Session::active())) {
			if (!empty($session['v']) && is_numeric($session['v'])) {
				$user = new query("users");
				$user = $user->select("users.*,users.groups as groups,users.statuses as statuses")->condition("users.id", $session['v'])->one();
				if (Session::validate($session, $user['password'], Etc::HMAC_KEY)) {
					$user['groups'] = is_null($user['groups']) ? array() : explode(",", $user['groups']);
					$user['statuses'] = is_null($user['statuses']) ? array() : explode(",", $user['statuses']);
					$this->user = $user;
				}
			}
		}
	}

	/**
	 * publish a topic to any subscribers or hooks
	 * @param string $topic the topic name you would like to publish
	 * @param mixed $args any additional parameters will be passed in an array to the subscriber
	 */
	function publish($topic, $args=null) {
		$sb = self::$instance;
		global $request;
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
	function import($loc) {
		$sb = self::$instance;
		$args = func_get_args();
		foreach($args as $l) {
			$parts = explode("/", $l);
			if (!in_array($parts[0], array("app", "core", "util", "var")) && file_exists(BASE_DIR."/modules/".$parts[0])) $parts[0] = "modules/".$parts[0];
			$path = implode("/", $parts);
			if (!isset($this->provided[$l])) include(BASE_DIR."/".$path.".php");
		}
	}

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
	
	function add_listener($object) {
		$this->listeners[] = $object;
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
