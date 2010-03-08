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
		efault($uris['prefix'], "app/views/");
		$uris['owner'] = $_SESSION[P("id")];
		if (!empty($uris['id'])) unset($uris['id']); 
		$uris['template'] = "templates/$uris[template]";
		$template = file_get_contents("$uris[prefix]$uris[template]");
		if (false !== ($start = strpos($template, "* cascade:"))) {
			$start += 10;
			$end = strpos($template, "\n", $start);
			$value = trim(substr($template, $start, $end-$start));
			if ($value == "disabled") $uris['check_path'] = "0";
		}
		return $this->store($uris);
	}
	
	function update() {
		$uris = $_POST['uris'];
		if (!isset($uris['id'])) return array("title" => array("missing_id" => "unidentified submission"));
		if (!empty($uris['template'])) $uris['template'] = "templates/$uris[template]";
		unset($uris['path']);
		$row = $this->query("where:id='$uris[id]'  limit:1");
		$template = file_get_contents("$row[prefix]$uris[template]");
		if (false !== ($start = strpos($template, "* cascade:"))) {
			$start += 10;
			$end = strpos($template, "\n", $start);
			$value = trim(substr($template, $start, $end-$start));
			if ($value == "disabled") $uris['check_path'] = "0";
			else $uris['check_path'] = 1;
		} else $uris['check_path'] = 1;
		if (false !== ($start = strpos($template, "* options:"))) {
			$start += 10;
			$end = strpos($template, "\n", $start);
			$values = explode(",", trim(substr($template, $start, $end-$start)));
			$uris['options'] = array();
			foreach($values as $op) {
				$uris['options'][$op] = $uris[$op];
				unset($uris[$op]);
			}
			$uris['options'] = serialize($uris['options']);
		}
		$errors = $this->store($uris);
		if (empty($errors)) {
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
	
	function child_ids($uid) {
		$prefix = array($uid);
		$children = $this->query("where:parent=$uid");
		if (!empty($children)) foreach($children as $kid) $prefix = array_merge($prefix, uri_list($kid['id']));
		return $prefix;
	}

}
?>
