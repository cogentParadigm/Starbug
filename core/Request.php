<?php
/**
* FILE: core/Request.php
* PURPOSE: provide data and errors, start session, send form data to models, and locate the request path
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
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
include("core/db/Table.php");
class Request {

	var $db;
	var $payload;
	var $errors;
	var $path;
	var $uri;
	var $groups;
	var $statuses;

	function Request($data) {
		$this->groups = array(
			"root"			=> 1,
			"user"			=> 2
		);
		$this->statuses = array(
			"deleted"     => 1,
			"inactive"    => 2,
			"active"      => 4,
			"cancelled"   => 8,
			"pending"     => 16
		);
		//connect to database
		$this->db = $data;
		//start session
		session_start();
		if (!isset($_SESSION[P('id')])) $_SESSION[P('id')] = $_SESSION[P('memberships')] = 0;
		//init errors array
		$this->errors = array();
		//manipulate data if necessary
		$this->check_post();
		//locate request
		$this->path = (strpos($_SERVER['REQUEST_URI'], BASE_DIR) === false) ? substr($_SERVER['REQUEST_URI'], 1) : end(split(BASE_DIR."/", $_SERVER['REQUEST_URI']));
		efault($this->path, Etc::DEFAULT_PATH);
		$this->locate();
		//execute
		$this->execute();
 	}

	protected function get($key) {return D($key, $this->db);}

	protected function has($name) {return D_exists($name);}

	protected function locate() {
		if (Etc::DB_NAME != "") {
			$this->payload = $this->get('uris')->find("*", "'".$this->path."' LIKE CONCAT(".Etc::PATH_COLUMN.", '%')", "ORDER BY CHAR_LENGTH(".Etc::PATH_COLUMN.") DESC LIMIT 1")->fields();
			if (empty($this->payload)) $this->path = (($this->path == Etc::DEFAULT_PATH)?Etc::DEFAULT_PATH:"missing");
		}
		$this->uri = split("/", $this->path);
	}

	protected function post_act($key, $value) {
		if (($object = $this->get($key)) && method_exists($object, $value)) {
			$permits = isset($_POST[$key]['id']) ? $object->get_object_permits("*", $value, "obj.id='".$_POST[$key]['id']."'") : $object->get_table_permits($value);
			if ($permits->RecordCount() > 0) $errors = $object->$value();
			else $errors = array("forbidden" => true);
			$this->errors = array_merge_recursive($this->errors, array($key => $errors)); 
		}
	}

	private function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

	private function execute() {
		if (file_exists("app/nouns/".$this->payload['template'].".php")) include("app/nouns/".$this->payload['template'].".php");
		else if (file_exists("core/app/nouns/".$this->payload['template'].".php")) include("core/app/nouns/".$this->payload['template'].".php");
		else if (file_exists("app/nouns/".Etc::DEFAULT_TEMPLATE.".php")) include("app/nouns/".Etc::DEFAULT_TEMPLATE.".php");
		else include("core/app/nouns/Starbug.php");
	}

}
?>	
