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
	public $host = "";
	public $language = "en";
	public $path = "";
	public $uri = array();
	public $query_string = ''; //GET
	public $query = array(); //GET
	public $data = array(); //POST
	public $server = array();
	public $files = array();
	public $cookies = array();
	public $format = "html";
	public $directory;

	public function _construct($request_path, $options=array()) {
		$options = array_replace(array(
			'server' => array(),
			'query' => array(),
			'data' => array(),
			'files' => array(),
			'cookies' => array(),
			'directory' => '/'
		), $options);

		$this->directory = $options['directory'];
		$this->server = $options['server'];
		$this->query = $options['query'];
		$this->data = $options['data'];
		$this->files = $options['cookies'];
		$this->files = $options['files'];

		$this->host = empty($options['host']) ? $this->server['HTTP_HOST'] : $options['host'];
		$parts = explode(".", $host);
		if (count($parts) > 2 && strlen($parts[0]) == 2) $this->language = $parts[0];

		//if the path includes the base_dir, we remove it. otherwise we just remove the the leading slash
		$this->path = substr($request_path, strlen($this->directory));

		//if the path contains a query string, split it off and save it to $this->query
		if (false !== strpos($this->path, "?")) list($this->path, $this->query_string) = explode("?", $this->path, 2);

		//if the path includes a format (such as .html, .json, .xml etc..) split it off and save it to $this->format
		$file = end(explode("/", $this->path));
		if (false !== strpos($file, ".")) {
			$this->format = end(explode(".", $file));
			$this->path = substr($this->path, 0, -(strlen($this->format)+1));
		}

		$this->uri = explode("/", $this->path);
	}
}
