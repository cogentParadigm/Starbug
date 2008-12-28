<?php
//class Request
include("core/db/Table.php");
class Request {

	var $db;
	var $payload;
	var $errors;
	var $path;
	var $uri;

	function Request($data) {
		//identify request URI
		$this->path = end(split(BASE_DIR."/", $_SERVER['REQUEST_URI']));
		$this->uri = split("/", $this->path);
		//connect to database
		$this->db = $data;
		//start session
		session_start();
		//init errors array
		$this->errors = array();
		//manipulate data if necessary
		$this->check_post();
		//abstract results
		$this->payload = array();
		$this->fetch_payload();
		//deliver payload
		$this->deliver($this->payload);
 	}

	protected function get_object($key) {if (D_exists($key)) return D($key, $this->db);}

	protected function set_payload($name, $template) {$this->payload["page"] = $name; $this->payload["template"] = $template;}

	protected function has_object($name) {return D_exists($name);}

	protected function fetch_payload() {
		dfault($this->path, Etc::DEFAULT_PATH);
		//queue payloads
		if (Etc::DB_NAME != "") {
			$elements = $this->get_object('Elements');
			$payload = $elements->find(Etc::PATH_COLUMN.", ".Etc::TEMPLATE_COLUMN, "'".$this->path."' LIKE ".Etc::PATH_COLUMN." + '%'", "ORDER BY CHAR_LENGTH(".Etc::PATH_COLUMN.") DESC LIMIT 1");
			if (empty($payload)) $this->set_payload((($this->path == Etc::DEFAULT_PATH)?Etc::DEFAULT_PATH:"missing"), Etc::DEFAULT_TEMPLATE);
			else $this->set_payload($payload[Etc::PATH_COLUMN], $payload[Etc::TEMPLATE_COLUMN]);
		} else $this->set_payload("", "");
	}

	protected function post_act($key, $value) {
		if ($this->has_object($key)) {
			$object = $this->get_object($key);
			if (method_exists($object, $value)) $this->errors = array_merge($this->errors, $object->$value());
		}
	}

	private function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

	private function deliver($payload) {
		if (file_exists("app/elements/".$payload['template'].".php")) include("app/elements/".$payload['template'].".php");
		else if (file_exists("core/app/elements/".$payload['template'].".php")) include("core/app/elements/".$payload['template'].".php");
		else if (file_exists("core/app/elements/Starbug.php")) include("core/app/elements/Starbug.php");
	}

}
?>
