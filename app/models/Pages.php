<?php
class Pages extends Table {

	var $defaults = array('sort_order' => '0');
	var $uniques = array("name");
	var $lengths = array('title' => '128', 'content' => '5000', 'template' => '64', 'name' => '32');
	
	function create() {
		$page = $_POST['page'];
		if (!isset($page['id'])) $page['created'] = date("Y-m-d H:i:s");
		else if (isset($page['created'])) unset($page['created']);
		$page['modified'] = date("Y-m-d H:i:s");
		$template = "templates/$page[template]";
		unset($page['template']);
		$collective = $page['collective'];
		unset($page['collective']);
		$errors = $this->store($page);
		if (empty($errors) && empty($page['id'])) $this->db->Execute("INSERT INTO ".P("uris")." (path, template, visible, collective) VALUES ('$page[name]', '$template', '1', '$collective')");
		return $errors;
	} 

	function delete() {
		return $this->remove("id='".$_POST['page']['id']."'");
	}
	
	function render($container, $name=null) {
		global $sb;
		global $request;
		if (!$name) $name = current($request->uri);
		$leafs = $this->db->Execute("SELECT * FROM ".P("leafs")." WHERE page='$name' && container='$container' ORDER BY position ASC")->GetRows();
		foreach ($leafs as $leaf) {
			include("app/nouns/leafs/$leaf[leaf].php");
		}
	}
}
?>
