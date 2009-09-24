<?php
/**
* FILE: core/Request.php
* PURPOSE: provide data and errors, start session, send form data to models, and locate the request path
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

	var $payload;					// row in uris table - assoc
	var $path;						// request path - string
	var $uri;							// request path - array
	var $file;						// noun file path
	var $tags;
	var $groups;
	var $statuses;

	function Request() {
		$this->tags = array(array("tag" => "global", "raw_tag" => "global"));
		$this->groups = array(
			"root"			=> 1,
			"user"			=> 2
		);
		$this->statuses = array(
			"deleted"     => 1,
			"inactive"    => 2,
			"active"      => 4,
			"cancelled"   => 8,
			"pending"     => 16,
			"private"			=> 32
		);
		//manipulate data if necessary
		$this->check_post();
		//locate request
		$this->path = (strpos($_SERVER['REQUEST_URI'], BASE_DIR) === false) ? substr($_SERVER['REQUEST_URI'], 1) : end(explode(BASE_DIR."/", $_SERVER['REQUEST_URI']));
		efault($this->path, Etc::DEFAULT_PATH);
		$this->locate();
		//execute
		$this->execute();
 	}

	private function check_path($prefix, $base, $current) {
		if (empty($current)) $current = "default";
		if (file_exists("$prefix$base$current.php")) return $prefix.$base.$current.".php";
		else if (file_exists("$prefix$base$current")) return $this->check_path($prefix, "$base$current/", next($this->uri));
		else {
			header("HTTP/1.1 404 Not Found");
			$this->path="missing";
			$this->uri = array("missing");
			$this->payload = array("path" => "missing", "template" => Etc::DEFAULT_TEMPLATE, "prefix" => $prefix);
			return $prefix."missing.php";
		}
	}

	protected function locate() {
		global $sb;
		if (Etc::DB_NAME != "") $this->payload = $sb->get('uris')->find("*", "'".$this->path."' LIKE CONCAT(".Etc::PATH_COLUMN.", '%')", "ORDER BY CHAR_LENGTH(".Etc::PATH_COLUMN.") DESC LIMIT 1")->fields();
		else $this->payload = array("path" => "default", "template" => "Starbug", "check_path" => "1", "prefix" => "core/app/nouns/");
		print_r($pthis->payload);
		if (empty($this->payload)) {
			$this->path = "";
			$this->payload['prefix'] = ($_SESSION[P('memberships')] == 1) ? "core/app/nouns/" : "app/nouns/";
		} else if ($this->payload['id']) $this->tags = array_merge($this->tags, $sb->db->Execute("SELECT DISTINCT tag, raw_tag FROM ".P("uris")." as obj INNER JOIN ".P("uris_tags")." as linker ON obj.id=linker.object_id INNER JOIN ".P("system_tags")." as tags ON linker.tag_id=tags.id WHERE obj.id='".$this->payload['id']."'")->GetRows());
		$this->uri = explode("/", $this->path);
		if ($this->payload['check_path'] !== '0') $this->file = $this->check_path($this->payload['prefix'], "", current($this->uri));
	}

	protected function execute() {
		global $sb;
		if (file_exists($this->payload['prefix'].$this->payload['template'].".php")) include($this->payload['prefix'].$this->payload['template'].".php");
		else include("core/app/nouns/Starbug.php");
	}

	protected function post_act($key, $value) {
		global $sb;
		if (($object = $sb->get($key)) && method_exists($object, $value)) {
			$permits = isset($_POST[$key]['id']) ? $object->get_object_permits("*", $value, "obj.id='".$_POST[$key]['id']."'") : $object->get_table_permits($value);
			if (($permits->RecordCount() > 0) || ($_SESSION[P('memberships')] & 1)) $errors = $object->$value();
			else $errors = array("forbidden" => true);
			$sb->errors = array_merge_recursive($sb->errors, array($key => $errors));
		}
	}

	protected function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

}
?>
