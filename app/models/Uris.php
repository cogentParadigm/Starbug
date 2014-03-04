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
		$row = $this->get($uris['id']);
		$this->store($uris);
		if (!errors()) {
			$uid = $uris['id'];
			$blocks = get("blocks", array("uris_id" => $uris['id']));
			foreach ($blocks as $block) {
				$key = 'block-'.$block['region'].'-'.$block['position'];
				if (!empty($_POST[$key])) store("blocks", array("id" => $block['id'], "content" => $_POST[$key]["content"]));
			}
		}
	}

	function delete($uris) {
		$id = $uris['id'];
		remove("blocks", "uris_id:".$uris['id']);
		return $this->remove("id:".$id);
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
	
	function query_admin($query, &$ops) {
		$query->select("uris.*,uris.statuses.id as statuses");
		if (!logged_in("admin")) $query->action("read");
		$query->condition("uris.prefix", "app/views/");
		$query->condition("uris.statuses", "deleted", "!=");
		if (!empty($ops['type'])) {
			$query->condition("uris.type", $ops['type']);
		}
		if (!empty($ops['status'])) $query->condition("uris.statuses.id", $ops['status']);
		efault($ops['orderby'], "title");
		$query->sort($ops['orderby']);
		return $query;
	}

}
?>
