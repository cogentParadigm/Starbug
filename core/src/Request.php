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
	public $payload = array();
	/**
	* @var string the request host
	*/
	public $host = "";
	/**
	* @var string the request language
	*/
	public $language = "en";
	/**
	 * @var string the request path
	 */
	public $path = "";
	/**
	 * @var array the request path split by segment
	 */
	public $uri = array();
	/**
	 * @var string the query string
	 */
	public $query = "";
	/**
	 * @var string the template
	 */
	public $template = "";
	/**
	 * @var string the requested format (xml, json, xhr)
	 */
	public $format = "html";
	/**
	 * @var string the stylesheet to use
	 */
	public $theme = "";
	/**
	 * @var string layout
	 */
	public $layout = "";
	/**
	 * @var string the file path of the view
	 */
	public $file = "";
	/**
	 * @var array the tags applied to the requested URI
	 */
	public $tags;

	public $context;

	private $locator;


	/**
	 * constructor. initiates tags and postback
	 */
 function __construct(TemplateInterface $context, ResourceLocatorInterface $locator) {
	 $this->context = $context;
	 $this->locator = $locator;
	 $this->tags = array(array("term" => "global", "slug" => "global"));
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
		public function set_path($host, $request_path) {
		$this->host = $host;
		$parts = explode(".", $host);
		if (count($parts) > 2 && strlen($parts[0]) == 2) $this->language = $parts[0];

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
		if (empty($this->path)) $this->path = settings("default_path");
		}

	/**
	 * return the path to the postback. called when a form submission contains errors
	 */
		public function return_path() {
		 if (!empty($_POST['postback'])) $this->path = $_POST['postback'];
		}

	/**
	 * run a model action if permitted
	 * @param string $key the model name
	 * @param string $value the function name
	 */
		function post_action($key, $value) {
		 if ($object = sb($key)) {
			 error_scope($key);
		  if (isset($_POST[$key]['id'])) {
		   $permits = query($key)->action($value)->condition($key.".id", $_POST[$key]['id'])->one();
		  /*
          if ($permits && $_POST['modified'][$key] !== $permits['modified']) {
              error("This content has changed since you started editing it.", "global", $key);
              $this->return_path();
              return;
          }
          */
		  } else {
			  $permits = query("permits")->action($value, $key)->one();
		  }
			 if ($permits || logged_in("root")) $object->$value($_POST[$key]);
			 else $this->forbidden();
			 error_scope("global");
			 if (errors($key)) $this->return_path();
		 }
		}

	/**
	 * check $_POST['action'] for posted actions and run them through post_act
	 */
		function check_post() {
		 if (!empty($_POST['action'])) {
			 //validate csrf token for authenticated requests
		  if (logged_in()) {
		   $validated = false;
		   if (!empty($_COOKIE['oid']) && !empty($_POST['oid']) && $_COOKIE['oid'] === $_POST['oid']) $validated = true;
		   if (true !== $validated) {
			   $this->return_path();
			   return;
		   }
		  }
			 //execute post actions
			 foreach ($_POST['action'] as $key => $val) $this->post_action(normalize($key), normalize($val));
		 }
		}

	/**
	 * lookup the path in the uris table and set the payload, tags, uri, and file. also will delivers 404, or 403 headers if needed
	 */
		public function locate() {
			//set up a query for uris where the path is a prefix of the current path
			$query = "select:uris.*,uris.categories.slug as categories  where:'".$this->path."' LIKE CONCAT(path, '%')";
			//run the query, looking for read permits
			$this->payload = query("uris", $query."  action:read")->sort("CHAR_LENGTH(path) DESC")->one();
		 if (empty($this->payload)) { //if we find nothing, query without looking for permits
			 $row = query("uris", $query)->one();
			 if (!empty($row)) $this->forbidden(); //if we find something that means we don't have permission to see it, so show the forbidden page
			 else $this->missing(); //if we don't find anything, there is nothing there, so show the missing page
		 }
			$this->uri = explode("/", ($this->path = ((empty($this->payload)) ? "" : $this->path)));
			efault($this->payload['format'], $this->format);
			foreach ($this->payload as $k => $v) if ($k != "path") $this->{$k} = $v;
			efault($this->theme, settings("theme"));
			if (empty($this->layout)) $this->layout = empty($this->type) ? "views" : $this->type;
			$this->locator->set("theme", "app/themes/".$this->theme);
		}

	/**
	 * calls $sb to to check for any post actions, runs locate, and loads the requested file
	 */
		public function execute() {
			ob_start();
			$this->check_post();
			$this->locate();
			$target = empty($this->template) ? $this->format : $this->template;
			$this->context->render($target, array("request" => $this));
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
