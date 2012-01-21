<?php
class Uris extends UrisModel {

	function create($uris) {
		queue("blocks", "type:text  region:content  position:1  uris_id:");
		$this->store($uris);
		if (!errors()) {
			redirect(uri("admin/uris/update"));
		} else {
			global $sb;
			if (errors("uris[title]") && empty($uris['path'])) unset($sb->errors['uris']['path']);
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

}
?>
