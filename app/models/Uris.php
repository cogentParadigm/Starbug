<?php
class Uris extends UrisModel {

	function create($uris) {
		if ($uris['template'] == "View") {
			$uris['template'] = "";
			$uris['check_path'] = "1";
		} else {
			if ($uris['template'] == "Page") $uris['template'] = "";
			$uris['check_path'] = "0";
		}
		queue("blocks", "type:text  region:content  position:1  uris_id:");
		$this->store($uris);
		if (!errors()) {
			redirect(uri("admin/uris/update"));
		}
	}
	
	function update($uris) {
		$row = $this->query("where:id='$uris[id]'  limit:1");
		$this->store($uris);
		if (!errors()) {
			$blocks = query("blocks", "where:uris_id=?", array($uris['id']));
			foreach ($blocks as $block) {
				$key = 'block-'.$block['region'].'-'.$block['position'];
				if (!empty($_POST[$key])) store("blocks", array("id" => $block['id'], "content" => $_POST[$key]["content"]));
			}
		}
	}

	function delete($uris) {
		$id = $uris['id'];
		return $this->remove("id='".$id."'");
	}

	function change_name($uris) {
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
