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
 * Request class. interprets the request (URI, $_GET, and $_POST) and serves the appropriate content
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
	 * constructor. initiates tags and postback
	 */
	function __construct($request_path) {
		$this->tags = array(array("term" => "global", "slug" => "global"));
		$this->set_path($request_path);
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
	 */
 	public function set_path($request_path) {
		//if the path includes the base_dir, we remove it. otherwise we just remove the the leading slash
		$this->path = substr($request_path, strlen(Etc::WEBSITE_URL));

		//if the path contains a query string, split it off and save it to $this->query
		if (false !== strpos($this->path, "?")) list($this->path, $this->query) = explode("?", $this->path, 2);
		
		//if the path includes a format (such as .html, .json, .xml etc..) split it off and save it to $this->format
		$file = end(explode("/", $this->path));
		if (false !== strpos($file, ".")) {
			$this->format = end(explode(".", $file));
			$this->path = substr($this->path, 0, -(strlen($this->format)+1));
		}

		//if we are left with an empty path, set it to the default path
		efault($this->path, settings("default_path"));
	}

	/**
	 * return the path to the postback. called when a form submission contains errors
	 */
	public function return_path() {if (!empty($_POST['postback'])) $this->path = $_POST['postback'];}

	/**
	 * run a model action if permitted
	 * @param string $key the model name
	 * @param string $value the function name
	 */
	function post_action($key, $value) {
		if ($object = sb($key)) {
			error_scope($key);
			$permits = isset($_POST[$key]['id']) ? query($key, "action:$value  where:$key.id='".$_POST[$key]['id']."'") : query($key, "action:$value  priv_type:table");
			if ($permits || logged_in("root")) $object->$value($_POST[$key]);
			else $this->forbidden();
			error_scope("global");
			if (errors($key)) $this->return_path();
		}
	}

	/**
	 * check $_POST['action'] for posted actions and run them through post_act
	 */
	function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_action($key, $val);}

	/**
	 * lookup the path in the uris table and set the payload, tags, uri, and file. also will delivers 404, or 403 headers if needed
	 */
	public function locate() {
		//include the router hook for the active database driver
		//this should set $this-payload and $this->tags
		include("modules/db/hooks/".Etc::DB_TYPE.".router.php");
		$this->uri = explode("/", ($this->path = ((empty($this->payload)) ? "" : $this->path)));
		efault($this->payload['format'], $this->format);
		foreach ($this->payload as $k => $v) if ($k != "path") $this->{$k} = $v;
		efault($this->theme, settings("theme"));
		efault($this->layout, $this->type);
		if ($this->type == 'View') $this->file = locate_view($this->uri);
	}

	/**
	 * calls $sb to to check for any post actions, runs locate, and loads the requested file
	 */
	public function execute() {
		ob_start();
		global $sb;
		global $request;
		$this->check_post();
		$this->locate();
		if (!empty($_GET['template'])) {
			foreach ($_POST as $k => $v) assign($k, $v);
			render($_GET['template'], $_GET['scope']);
		} else if (!empty($this->template)) render($this->template);
		else render($this->format);
		ob_end_flush();
	}

	/**
	 * sends a 404 and sets the payload, path, and uri
	 */
	public function missing() {
		header("HTTP/1.1 404 Not Found");
		$this->path="missing";
		$this->locate();
	}
	
	/**
	 * sends a 403 and sets the payload, path, and uri
	 */
	public function forbidden() {
		header("HTTP/1.1 403 Forbidden");
		$this->path = "forbidden";
		$this->locate();
	}

}
?>
