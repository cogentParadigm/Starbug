<?php
/**
* FILE: core/Request.php
* PURPOSE: interprets the request URI and initiates action
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
class Request {

	var $payload;					// row in uris table
	var $path;						// request path - string
	var $uri;							// request path - array
	var $file;						// noun file path
	var $tags;
	var $groups;
	var $statuses;
	var $base_dir;

	function Request($groups, $statuses) {
		$this->tags = array(array("tag" => "global", "raw_tag" => "global"));
		$this->groups = $groups;
		$this->statuses = $statuses;
		if (!isset($_SESSION[P('postback')])) $_SESSION[P('postback')] = $_SERVER['REQUEST_URI'];
 	}
 	
 	function set_path($request_path, $base_dir="") {
		if (empty($base_dir)) $base_dir = $this->base_dir;
		else $this->base_dir = $base_dir;
		$this->path = (false === ($base_pos = strpos($request_path, $base_dir))) ? substr($request_path, 1) : substr($request_path, $base_pos+strlen($base_dir)+1);
		if (false !== strpos($this->path, "?")) $this->path = reset(explode("?", $this->path));
		efault($this->path, Etc::DEFAULT_PATH);
	}

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
	
	function return_path() {$this->path = $_SESSION[P('postback')];}

	function locate() {
		global $sb;
		$query = "where:'".$this->path."' LIKE CONCAT(path, '%') ORDER BY CHAR_LENGTH(path) DESC  limit:1";
		$this->payload = $sb->query("uris", $query."  action:read");
		if (empty($this->payload)) {
			$row = $sb->query("uris", $query);
			if (!empty($row)) $this->forbidden();
			else $this->missing();
		}
		$this->tags = array_merge($this->tags, $sb->query("uris,tags", "select:DISTINCT tag, raw_tag  where:uris.id='".$this->payload['id']."'", true));
		print_r($tthis->tags);
		$this->uri = explode("/", ($this->path = ((empty($this->payload)) ? "" : $this->path )));
		if ($this->payload['check_path'] !== '0') $this->file = $this->check_path($this->payload['prefix'], "", current($this->uri));
	}

	public function execute() {
		global $sb;
		$sb->check_post();
		$this->locate();
		if (!empty($_GET['x'])) include($this->file);
		else include($this->payload['prefix'].$this->payload['template'].".php");
		$_SESSION[P('postback')] = $this->path;
	}
	
	public function missing() {
		header("HTTP/1.1 404 Not Found");
		$this->payload = array("path" => "missing", "template" => Etc::DEFAULT_TEMPLATE, "prefix" => "app/views/", "check_path" => '0');
		$this->path="missing";
		$this->uri = array("missing");
	}
	
	public function forbidden() {
		header("HTTP/1.1 403 Forbidden");
		$this->payload = array("path" => "forbidden", "template" => Etc::DEFAULT_TEMPLATE, "prefix" => "app/views/", "check_path" => '0');
		$this->path = "forbidden";
		$this->uri = array("forbidden");
	}

}
?>
