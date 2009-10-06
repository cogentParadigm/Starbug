<?php
class Pages extends Table {

	var $filters = array(
		"name" => "unique:	length:32",
		"title" => "length:128",
		"template" => "length:64",
		"content" => "length:5000",
		"sort_order" => "default:0"
	);
	
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
		$leafs = $sb->query("leafs", "where:page='$name' && container='$container' ORDER BY position ASC");
		foreach ($leafs as $leaf) include("app/nouns/leafs/$leaf[leaf]/show.php");
	}
}
?>
