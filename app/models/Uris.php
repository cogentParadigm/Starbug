<?php
class Uris {

	function create($uris) {
		if ($uris['type'] != "View" && $uris['type'] != $_POST['type']) {
			$uris['layout'] = $uris['type'];
			$uris['type'] = $_POST['type'];
		}
		if ($_POST['type'] == "Post") $uris['path'] = "blog/".$uris['path'];
		queue("blocks", "type:text  region:content  position:1  uris_id:");
		efault($uris['categories'], array());
		$categories = $uris['categories'];
		unset($uris['categories']);
		$this->store($uris);
		if (!errors()) {
			$uid = $this->insert_id;
			foreach ($categories as $tid) store("uris_categories", "uris_id:$uid  terms_id:$tid");
			if ($_POST['type'] == "Page") redirect(uri("admin/uris/update"));
			else redirect(uri("admin/".strtolower($_POST['type'])."s/update"));
		} else {
			global $sb;
			if (errors("uris[title]") && empty($uris['path'])) unset($sb->errors['uris']['path']);
		}
	}
	
	function update($uris) {
		if ($uris['type'] != "View" && $uris['type'] != $_POST['type']) {
			$uris['layout'] = $uris['type'];
			$uris['type'] = $_POST['type'];
		}
		if ($_POST['type'] == "Post") $uris['path'] = "blog/".$uris['path'];
		$row = $this->query("where:id='$uris[id]'  limit:1");
		efault($uris['categories'], array());
		$categories = $uris['categories'];
		unset($uris['categories']);
		$this->store($uris);
		if (!errors()) {
			$uid = $uris['id'];
			remove("uris_categories", "uris_id=$uid".(empty($categories) ? "" : " && terms_id NOT IN (".implode(", ", $categories).")"));
			foreach ($categories as $tid) {
				$exists = query("uris_categories", "where:uris_id=? && terms_id=?  limit:1", array($uid, $tid));
				if (!$exists) store("uris_categories", "uris_id:$uid  terms_id:$tid");
			}
			$blocks = query("blocks", "where:uris_id=?", array($uris['id']));
			foreach ($blocks as $block) {
				$key = 'block-'.$block['region'].'-'.$block['position'];
				if (!empty($_POST[$key])) store("blocks", array("id" => $block['id'], "content" => $_POST[$key]["content"]));
			}
		}
	}

	function delete($uris) {
		$id = $uris['id'];
		remove("blocks", "uris_id='".$uris['id']."'");
		return $this->remove("id='".$id."'");
	}
	
	function apply_tags() {
		global $sb;
		$tags = explode(",", $_POST['tags']);
		$uid = $_POST['uris']['id'];
		foreach($tags as $tag) tag("uris_tags", $uid, trim($tag));
	}
	
	function remove_tag() {
		global $sb;
		$tag = $_POST['tag'];
		$uri = $_POST['uris']['id'];
		untag("uris_tags", $uri, $tag);
	}
	
	function query_admin($query) {
		$query['where'] += array("prefix='app/views/'", "type='Page'", "!(uris.status & 1)");
		$query['orderby'] = "title";
		return $query;
	}

}
?>
