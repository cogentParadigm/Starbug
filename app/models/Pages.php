<?php
class Pages extends Table {

	var $filters = array(
		"name" => "unique:true	length:32",
		"title" => "length:128",
		"template" => "length:64",
		"content" => "length:5000",
		"sort_order" => "default:0"
	);
	
	function create() {
		global $sb;
		$page = $_POST['pages'];
		if (!empty($page['id'])) unset($page['id']); 
		$page['created'] = date("Y-m-d H:i:s");
		$page['modified'] = date("Y-m-d H:i:s");
		$template = "templates/$page[template]";
		$collective = $page['collective'];
		unset($page['template']);
		unset($page['collective']);
		$errors = $this->store($page);
		$uri = array("path" => $page['name'], "template" => $template, "visible" => 1, "collective" => $collective);
		if (empty($errors)) $sb->store("uris", $uri);
		return $errors;
	}

	function update() {
		global $sb;
		$page = $_POST['pages'];
		if (!isset($page['id'])) return array("id" => array("missing" => true));
		unset($page['created']);
		$page['modified'] = date("Y-m-d H:i:s");
		if (!empty($page['template'])) {
			$template = "templates/$page[template]";
			unset($page['template']);
		}
		if (!empty($page['collective'])) $collective = $page['collective'];
		unset($page['collective']);
		if (!empty($page['name'])) $pagename = $page['name'];
		unset($page['name']);
		$errors = $this->store($page);
		if (empty($errors)) {
			if (empty($pagename)) {
				$row = $sb->query("pages", "where:id='$page[id]'	limit:1");
				$pagename = $row['name'];
			}
			$leafs = $sb->query("leafs", "where:page='$pagename' ORDER BY container ASC, position ASC");
			foreach($leafs as $leaf) include("app/nouns/leafs/$leaf[leaf]/save.php");
			foreach($_POST['new-leaf'] as $container => $leaf) if (!empty($leaf)) include("app/nouns/leafs/$leaf/create.php");
			unset($_POST['new-leaf']);
			foreach($_POST['remove-leaf'] as $container => $leaf) if (!empty($leaf)) include("app/nouns/leafs/".end(explode(" ", $leaf))."/delete.php");
		}
		if (empty($errors) && ((!empty($collective)) || (!empty($collective)))) $this->db->Execute("UPDATE ".P("uris")." SET collective='$collective', template='$template' WHERE path='$pagename'");
		return $errors;
	} 

	function delete() {
		$this->remove("id='".$_POST['pages']['id']."'");
		unset($_POST['pages']);
		return array();
	}
	
	function change_name() {
		global $sb;
		$errors = array();
		$sb->query("uris", "where:path='$_POST[new_name]'");
		if ($sb->recordCount > 0) return array("nameExistsError" => true);
		$sb->db->Execute("UPDATE `".P("uris")."` SET path='$_POST[new_name]' WHERE path='$_POST[old_name]'");
		$sb->db->Execute("UPDATE `".P("pages")."` SET name='$_POST[new_name]' WHERE name='$_POST[old_name]'");
		$leafs = $sb->query("leafs", "select:DISTINCT leaf	where:page='$_POST[old_name]'");
		$sb->db->Execute("UPDATE `".P("leafs")."` SET page='$_POST[new_name]' WHERE page='$_POST[old_name]'");
		foreach($leafs as $leaf) $sb->db->Execute("UPDATE `".P($leaf['leaf'])."` SET page='$_POST[new_name]' WHERE page='$_POST[old_name]'");
		$page = $sb->query("pages", "where:name='$_POST[new_name]'	limit:1");
		$_POST['pages']['id'] = $page['id'];
		return $errors;
	}
	
	function render($container, $name=null) {
		global $sb;
		global $request;
		if (!$name) $name = current($request->uri);
		$leafs = $sb->query("leafs", "where:page='$name' && container='$container' ORDER BY position ASC");
		foreach ($leafs as $leaf) include("app/nouns/leafs/$leaf[leaf]/show.php");
	}
	
	function fields($container, $name) {
		global $sb;
		$fieldset = "";
		$leafs = $sb->query("leafs", "where:page='$name' && container='$container' ORDER BY position ASC");
		foreach ($leafs as $leaf) {
			include("app/nouns/leafs/$leaf[leaf]/fields.php");
			$fieldset .= $fields;
		}
		return $fieldset;
	}
}
?>
