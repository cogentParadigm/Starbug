<?php
class Uris extends Table {

	public $filters = array(
		"path" => "length:64  unique:true",
		"template" => "length:32",
		"title" => "length:128",
		"parent" => "default:0",
		"sort_order" => "default:0",
		"check_path" => "default:1",
		"prefix" => "default:app/views/",
		"options" => "default:"
	);

	function Uris($type) {
		parent::Table($type);
		$this->has_many("tags", "object_id", "uris_tags", "tag_id");
	}

	function create() {
		$uris = $_POST['uris'];
		$uris['template'] = "templates/$uris[template]";
		$this->set_check_path($uris, file_get_contents("$uris[prefix]$uris[template]"));
		return $this->store($uris);
	}
	
	function update() {
		global $sb;
		$uris = $_POST['uris'];
		unset($uris['path']);
		if (!empty($uris['template'])) $uris['template'] = "templates/$uris[template]";
		$row = $this->query("where:id='$uris[id]'  limit:1");
		//UNSET OLD TEMPLATE OPTIONS
		$this->remove_template_options($uris, file_get_contents("$row[prefix]$row[template].php"));
		// SET NEW TEMPLATE OPTIONS
		$template = file_get_contents("$row[prefix]$uris[template].php");
		$this->set_template_options($uris, $template);
		// SET CHECK_PATH VALUE
		$this->set_check_path($uris, $template);
		$errors = $this->store($uris);
		if (empty($errors)) {
			$pagename = $row['path'];
			$leafs = $sb->query("leafs", "where:page='$row[path]' ORDER BY container ASC, position ASC");
			foreach($leafs as $leaf) include("$row[prefix]leafs/$leaf[leaf]/save.php");
			foreach($_POST['new-leaf'] as $container => $leaf) if (!empty($leaf)) include("$row[prefix]leafs/$leaf/create.php");
			unset($_POST['new-leaf']);
			foreach($_POST['remove-leaf'] as $container => $leaf) if (!empty($leaf)) include("$row[prefix]leafs/".end(explode(" ", $leaf))."/delete.php");
			unset($_POST['remove-leaf']);
		}
		return $errors;
	}

	function delete() {
		$id = $_POST['uris']['id'];
		return $this->remove("id='".$id."'");
	}

	function change_name() {
		global $sb;
		$errors = array();
		$this->query("where:path='$_POST[new_name]'");
		if ($this->record_count > 0) return array("path" => array("exists" => "That path already exists"));
		$sb->db->exec("UPDATE `".P("uris")."` SET path='$_POST[new_name]' WHERE path='$_POST[old_name]'");
		$leafs = $sb->query("leafs", "select:DISTINCT leaf  where:page='$_POST[old_name]'");
		$sb->db->exec("UPDATE `".P("leafs")."` SET page='$_POST[new_name]' WHERE page='$_POST[old_name]'");
		foreach($leafs as $leaf) $sb->db->exec("UPDATE `".P($leaf['leaf'])."` SET page='$_POST[new_name]' WHERE page='$_POST[old_name]'");
		$uris = $sb->query("uris", "where:path='$_POST[new_name]'  limit:1");
		$_POST['uris']['id'] = $uris['id'];
		return $errors;
	}
	
	function render($container, $name=null) {
		global $sb;
		global $request;
		if (!$name) $name = current($request->uri);
		$leafs = $sb->query("leafs", "where:page='$name' && container='$container' ORDER BY position ASC");
		foreach ($leafs as $leaf) include("app/views/leafs/$leaf[leaf]/show.php");
	}
	
	function fields($container, $name) {
		global $sb;
		$fieldset = "";
		$leafs = $sb->query("leafs", "where:page='$name' && container='$container' ORDER BY position ASC");
		foreach ($leafs as $leaf) {
			include("app/views/leafs/$leaf[leaf]/fields.php");
			$fieldset .= $fields;
		}
		return $fieldset;
	}
	
	function apply_tags() {
		global $sb;
		$sb->import("util/tags");
		$tags = explode(",", $_POST['tags']);
		$uid = $_POST['uris']['id'];
		foreach($tags as $tag) tags::safe_tag("tags", "uris_tags", $_SESSION[P('id')], $uid, trim($tag));
	}
	
	function remove_tag() {
		global $sb;
		$sb->import("util/tags");
		$tag = $_POST['tag'];
		$uri = $_POST['uris']['id'];
		tags::delete_object_tag("tags", "uris_tags", $uri, $tag);
	}

	function remove_template_options(&$fields, $t) {
		if (false !== ($start = strpos($t, "* options:"))) {
			$start += 10;
			$end = strpos($t, "\n", $start);
			$values = explode(",", trim(substr($t, $start, $end-$start)));
			foreach($values as $op) unset($fields[$op]);
		}
	}

	function set_template_options(&$fields, $t) {
		if (false !== ($start = strpos($t, "* options:"))) {
			$start += 10;
			$end = strpos($t, "\n", $start);
			$values = explode(",", trim(substr($t, $start, $end-$start)));
			$fields['options'] = array();
			foreach($values as $op) {
				$fields['options'][$op] = $fields[$op];
				unset($fields[$op]);
			}
			$fields['options'] = serialize($fields['options']);
		}
	}
	
	function set_check_path(&$fields, $t) {
		if (false !== ($start = strpos($t, "* cascade:"))) {
			$start += 10;
			$end = strpos($t, "\n", $start);
			$value = trim(substr($t, $start, $end-$start));
			if ($value == "disabled") $fields['check_path'] = "0";
		}
		dfault($fields['check_path'], 1);
	}

}
?>
