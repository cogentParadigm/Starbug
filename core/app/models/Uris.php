<?php
namespace Starbug\Core;
class Uris extends UrisModel {

	function filter($item, $action) {
		if ($action == "copy") {
			if (!empty($item['uris_id'])) {
				$blocks = $this->db->query("blocks")->condition("uris_id", $item['uris_id'])->all();
				$item['blocks'] = array();
				foreach ($blocks as $block) {
					$item['blocks'][$block['region']."-".$block['position']] = $block['content'];
				}
			}
			unset($item['id']);
			unset($item['slug']);
			unset($item['path']);
		}
		return $item;
	}

}
?>
