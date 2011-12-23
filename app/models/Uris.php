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
		if ($uris['template'] == "View") {
			$uris['template'] = "";
			$uris['check_path'] = "1";
		} else {
			if ($uris['template'] == "Page") $uris['template'] = "";
			$uris['check_path'] = "0";
		}
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
