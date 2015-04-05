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
	public $db;
	/**
	 * @var array active user
	 */
	public $user = false;
	/**
	 * @var string holds the active scope (usually 'global' or a model name)
	 */
	public $active_scope = "global";
	/**
	 * @var array holds the utils that have been provided
	 */
	var $provided = array();
	/**
	 * @var array holds validation errors
	 */
	public $errors = array();
	/**
	 * @var array holds alerts
	 */
	var $alerts = array();
	/**
	 * @var array holds $db change listeners
	 */
	var $listeners = array();
	/**#@-*/
	static $instance;

	public $locator;
	public $config;
	public $macro;
	public $models;

	/**
	* constructor. connects to db and starts the session
	*/
 function __construct(DatabaseInterface $db, ResourceLocatorInterface $locator, ConfigInterface $config, MacroInterface $macro, ModelFactoryInterface $models) {
	$this->locator = $locator;
	$this->config = $config;
	$this->macro = $macro;
	$this->db = $db;
	$this->models = $models;
	if (defined("Etc::DEBUG")) $this->db->set_debug(Etc::DEBUG);
	self::$instance = $this;
	$this->start_session();
 }

	/**
	 * load the Session class and validate the current session if the user has a cookie
	 */
 function start_session() {
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
	 * import function. only imports once when used with provide
	 * @param string $loc path of file to import without '.php' at the end
	 */
 function import($loc) {
	$sb = self::$instance;
	$args = func_get_args();
	foreach ($args as $l) {
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
 function provide($loc) {
	$this->provided[$loc] = true;
 }

	/**
	 * get a model by name
	 * @param string $name the name of the model, such as 'users'
	 * @return the instantiated model
	 */
 function get($name) {
	return $this->models->get($name);
 }
}
