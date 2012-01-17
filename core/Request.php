<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
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
	var $payload = array();
	/**
	 * @var string the request path
	 */
	var $path = "";
	/**
	 * @var array the request path split by segment
	 */
	var $uri = array();
	/**
	 * @var string the query string
	 */
	var $query = "";
	/**
	 * @var string the template
	 */
	var $template = "";
	/**
	 * @var string the requested format (xml, json, xhr)
	 */
	var $format = "html";
	/**
	 * @var string the stylesheet to use
	 */
	var $theme = "";
	/**
	 * @var string layout
	 */
	var $layout = "";
	/**
	 * @var string the file path of the view
	 */
	var $file = "";
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
	var $base_dir = "";
	

	/**
	 * constructor. initiates tags and postback
	 */
	function __construct($groups, $statuses) {
		$this->tags = array(array("term" => "global", "slug" => "global"));
		$this->groups = $groups;
		$this->statuses = $statuses;
		if (!isset($_SESSION[P('postback')])) $_SESSION[P('postback')] = $_SERVER['REQUEST_URI'];
 	}

	/**
	 * set the path
	 * used to normalize relative paths, for example if starbug is installed at http://www.mydomain.com/starbug/
	 * the following:
	 * login
	 * /login
	 * /starbug/login (this is what we'd get from $_SERVER['REQUEST_URI'])
	 * would all translate to:
	 * login
	 * @param string $request_path the path
	 * @param string $base_dir (optional) the base directory (only needed to change or initialize the base directory)
	 */
 	public function set_path($request_path, $base_dir="") {
		if (empty($base_dir)) $base_dir = $this->base_dir;
		else $this->base_dir = $base_dir;

		//if the path includes the base_dir, we remove it. otherwise we just remove the the leading slash
		$this->path = (false === ($base_pos = strpos($request_path, $base_dir))) ? ltrim($request_path, "/") : substr($request_path, $base_pos+strlen($base_dir)+1);

		//if the path contains a query string, split it off and save it to $this->query
		if (false !== strpos($this->path, "?")) list($this->path, $this->query) = explode("?", $this->path, 2);
		
		//if the path includes a format (such as .html, .json, .xml etc..) split it off and save it to $this->format
		$file = end(explode("/", $this->path));
		if (false !== strpos($file, ".")) {
			$this->format = end(explode(".", $file));
			$this->path = substr($this->path, 0, -(strlen($this->format)+1));
		}

		//if we are left with an empty path, set it to the default path
		efault($this->path, Etc::DEFAULT_PATH);
	}

	/**
	 * return the path to the postback. called when a form submission contains errors
	 */
	public function return_path() {if (!empty($_POST['postback'])) $this->path = $_POST['postback'];}

	/**
	 * lookup the path in the uris table and set the payload, tags, uri, and file. also will delivers 404, or 403 headers if needed
	 */
	public function locate() {
		//set up a query for uris where the path is a prefix of the current path
		$query = "select:uris.*  where:(uris.status & 4) && '".$this->path."' LIKE CONCAT(path, '%') ORDER BY CHAR_LENGTH(path) DESC  limit:1";
		//run the query, looking for read permits
		$this->payload = query("uris", $query."  action:read");
		if (empty($this->payload)) { //if we find nothing, query without looking for permits
			$row = query("uris", $query);
			if (!empty($row)) $this->forbidden(); //if we find something that means we don't have permission to see it, so show the forbidden page
			else $this->missing(); //if we don't find anything, there is nothing there, so show the missing page
		}
		$this->tags = array_merge($this->tags, query("uris,terms via uris_tags", "select:DISTINCT term, slug  where:uris.id='".$this->payload['id']."'"));
		$this->uri = explode("/", ($this->path = ((empty($this->payload)) ? "" : $this->path )));
		if ($this->payload['type'] == 'View') $this->file = $this->check_path($this->payload['prefix'], "", current($this->uri));
		$this->theme = $this->payload['theme'];
		$this->layout = $this->payload['layout'];
		$this->template = $this->payload['template'];
		efault($this->theme, Etc::THEME);
		efault($this->layout, theme("layout", $this->theme));
		efault($this->format, $this->payload['format']);
	}

	/**
	 * calls $sb to to check for any post actions, runs locate, and loads the requested file
	 */
	public function execute() {
		global $sb;
		global $request;
		$sb->check_post();
		$this->locate();
		if (!empty($_GET['template'])) {
			foreach ($_POST as $k => $v) assign($k, $v);
			render($_GET['template'], $_GET['scope']);
		} else if (!empty($this->payload['template'])) render($this->payload['template']);
		else render($this->format);
	}

	/**
	 * sends a 404 and sets the payload, path, and uri
	 */
	public function missing() {
		header("HTTP/1.1 404 Not Found");
		$this->payload = query("uris", "where:path='missing'  limit:1");
		$this->path="missing";
		$this->uri = array("missing");
	}
	
	/**
	 * sends a 403 and sets the payload, path, and uri
	 */
	public function forbidden() {
		header("HTTP/1.1 403 Forbidden");
		$this->payload = query("uris", "where:path='forbidden'  limit:1");
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
