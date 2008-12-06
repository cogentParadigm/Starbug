<?php
//class Request
load_file("core/db/Table");
class Request {

	var $db;
	var $payload;
	var $errors;

	function Request($data) {
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
		//update request URI
		if (empty($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
			//Append the query string if it exists and isn't null
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
		}

		//deliver payload
		$this->deliver($this->payload);
 	}

	protected function get_object($key) {if (D_exists($key)) return D($key, $this->db);}

	protected function set_payload($name, $template) {$this->payload["page"] = $name; $this->payload["template"] = $template;}

	protected function has_object($name) {return D_exists($name);}

	protected function fetch_payload() {
		$_GET['page'] = dfault($_GET['page'], "Home");
		//queue payloads
		if (Etc::DB_NAME != "") {
			$elements = $this->get_object('Elements');
			$payload = $elements->find(Etc::PAGE_COLUMN.", ".Etc::TEMPLATE_COLUMN, Etc::PAGE_COLUMN."='".$_GET['page']."'");
			if (empty($payload)) $this->set_payload((empty($_GET['page'])?"":"Missing"), "");
			else $this->set_payload($payload[Etc::PAGE_COLUMN], $payload[Etc::TEMPLATE_COLUMN]);
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
		$page = $payload['page'];
		if (file_exists("app/elements/".$payload['template'].".php")) include("app/elements/".$payload['template'].".php");
		else if (file_exists("app/elements/App.php")) include("app/elements/App.php");
		else if (file_exists("core/app/elements/".$payload['template'].".php")) include("core/app/elements/".$payload['template'].".php");
		else if (file_exists("core/app/elements/CoreApp.php")) include("core/app/elements/CoreApp.php");
	}

}
?>
