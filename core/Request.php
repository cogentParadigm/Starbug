<?php
/**
 * This file is part of StarbugPHP
 * @file core/Request.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * Request class. interprets the request URI and serves the appropriate content
 * @ingroup core
 */
class Request {

	/**
	 * @var array the row in the uris table of the requested path
	 */
	var $payload;
	/**
	 * @var string the request path
	 */
	var $path;
	/**
	 * @var array the request path split by segment
	 */
	var $uri;
	/**
	 * @var string the query string
	 */
	var $query;
	/**
	 * @var string the requested format (xml, json, xhr)
	 */
	var $format;
	/**
	 * @var string the file path of the view
	 */
	var $file;
	/**
	 * @var array the tags applied to the requested URI
	 */
	var $tags;
	/**
	 * @var array the groups
	 */
	var $groups;
	/**
	 * @var statuses the statuses
	 */
	var $statuses;
	/**
	 * @var string the path of the base directory
	 */
	var $base_dir;

	/**
	 * constructor. initiates tags and postback
	 */
	function __construct($groups, $statuses) {
		$this->tags = array(array("tag" => "global", "raw_tag" => "global"));
		$this->groups = $groups;
		$this->statuses = $statuses;
		if (!isset($_SESSION[P('postback')])) $_SESSION[P('postback')] = $_SERVER['REQUEST_URI'];
 	}

	/**
	 * set the path
	 * @param string $request_path the path
	 * @param string $base_dir the base directory
	 */
 	public function set_path($request_path, $base_dir="") {
		if (empty($base_dir)) $base_dir = $this->base_dir;
		else $this->base_dir = $base_dir;
		$this->path = (false === ($base_pos = strpos($request_path, $base_dir))) ? substr($request_path, 1) : substr($request_path, $base_pos+strlen($base_dir)+1);
		if (false !== strpos($this->path, "?")) {
			$this->path = explode("?", $this->path, 2);
			$this->query = $this->path[1];
			$this->path = reset($this->path);
		}
		if (false !== strpos($this->path, ".")) {
			$this->path = explode("/", $this->path);
			$index = count($this->path)-1;
			if (false !== strpos($this->path[$index], ".")) {
				$end = explode(".", $this->path[$index]);
				$this->format = end($end);
				array_pop($end);
				$this->path[$index] = join(".", $end);
			}
			$this->path = join("/", $this->path);
		}
		efault($this->path, Etc::DEFAULT_PATH);
	}

	/**
	 * return the path to the postback. called when a form submission contains errors
	 */
	public function return_path() {$this->path = (empty($_POST['postback'])) ? $_SESSION[P('postback')] : $_POST['postback'];}

	/**
	 * lookup the path in the uris table and set the payload, tags, uri, and file. also will delivers 404, or 403 headers if needed
	 */
	public function locate() {
		global $sb;
		$query = "where:'".$this->path."' LIKE CONCAT(path, '%') ORDER BY CHAR_LENGTH(path) DESC  limit:1";
		$this->payload = $sb->query("uris", $query."  action:read");
		if (empty($this->payload)) {
			$row = $sb->query("uris", $query);
			if (!empty($row)) $this->forbidden();
			else $this->missing();
		}
		$this->tags = array_merge($this->tags, $sb->query("uris,tags", "select:DISTINCT tag, raw_tag  where:uris.id='".$this->payload['id']."'", true));
		$this->uri = explode("/", ($this->path = ((empty($this->payload)) ? "" : $this->path )));
		if ($this->payload['check_path'] !== '0') $this->file = $this->check_path($this->payload['prefix'], "", current($this->uri));
	}

	/**
	 * calls $sb to to check for any post actions, runs locate, and loads the requested file
	 */
	public function execute() {
		global $sb;
		$the_postback = $this->path;
		$sb->check_post();
		$this->locate();
		if ((!empty($_GET['x'])) || ($this->format == "xhr")) include($this->file);
		else include($this->payload['prefix'].$this->payload['template'].".php");
		$_SESSION[P('postback')] = $the_postback;
	}

	/**
	 * sends a 404 and sets the payload, path, and uri
	 */
	public function missing() {
		header("HTTP/1.1 404 Not Found");
		$this->payload = array("path" => "missing", "template" => Etc::DEFAULT_TEMPLATE, "prefix" => "app/views/");
		$this->path="missing";
		$this->uri = array("missing");
	}
	
	/**
	 * sends a 403 and sets the payload, path, and uri
	 */
	public function forbidden() {
		header("HTTP/1.1 403 Forbidden");
		$this->payload = array("path" => "forbidden", "template" => Etc::DEFAULT_TEMPLATE, "prefix" => "app/views/");
		$this->path = "forbidden";
		$this->uri = array("forbidden");
	}

	/**
	 * checks the path to see if a matching file exists
	 * @return the file to be loaded
	 */
	private function check_path($prefix, $base, $current) {
		if (empty($current)) $current = "default";
		if (file_exists("$prefix$base$current.php")) return $prefix.$base.$current.".php";
		else if (file_exists("$prefix$base$current")) return $this->check_path($prefix, "$base$current/", next($this->uri));
		else if (file_exists($prefix.$base."default.php")) return $prefix.$base."default.php";
		else {
			$this->missing();
			return $prefix."missing.php";
		}
	}
}
?>
