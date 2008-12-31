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
		//connect to database
		$this->db = $data;
		//start session
		session_start();
		//init errors array
		$this->errors = array();
		//locate request
		$this->path = end(split(BASE_DIR."/", $_SERVER['REQUEST_URI']));
		dfault($this->path, Etc::DEFAULT_PATH);
		$this->locate();
		//manipulate data if necessary
		$this->check_post();
		//execute
		$this->execute();
 	}

	protected function get($key) {return D($key, $this->db);}

	protected function set_payload($basepath, $template) {$this->payload["path"] = $basepath; $this->payload["template"] = $template;}

	protected function has($name) {return D_exists($name);}

	protected function locate() {
		if ((Etc::DB_NAME != "") && ($this->payload = $this->get('elements')->find(Etc::PATH_COLUMN.", ".Etc::TEMPLATE_COLUMN, "'".$this->path."' LIKE CONCAT(".Etc::PATH_COLUMN.", '%')", "ORDER BY CHAR_LENGTH(".Etc::PATH_COLUMN.") DESC LIMIT 1")->fields())) if (empty($this->payload)) $this->path = (($this->path == Etc::DEFAULT_PATH)?Etc::DEFAULT_PATH:"missing");
		$this->uri = split("/", $this->path);
	}

	protected function post_act($key, $value) { if (($object = $this->get($key)) && method_exists($object, $value)) $this->errors = array_merge($this->errors, array($key => $object->$value())); }

	private function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

	private function execute() {
		if (file_exists("app/elements/".$this->payload['template'].".php")) include("app/elements/".$this->payload['template'].".php");
		else if (file_exists("core/app/elements/".$this->payload['template'].".php")) include("core/app/elements/".$this->payload['template'].".php");
		else if (file_exists("core/app/elements/Starbug.php")) include("core/app/elements/Starbug.php");
	}

}
?>
