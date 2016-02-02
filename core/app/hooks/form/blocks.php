<?php
namespace Starbug\Core;
class hook_form_blocks extends FormHook {
	function __construct(DatabaseInterface $db, RequestInterface $request) {
		$this->db = $db;
		$this->request = $request;
	}
	function build($form, &$control, &$field) {
		$containers = array(array("region" => "content", "position" => 1, "content" => "", "type" => "text"));
		$data = $this->request->getPost();
		$item_id = $form->get("id");
		if (!empty($item_id)) {
			$containers = $this->db->query("blocks")->condition("uris_id", $form->get("uris_id"))->sort("position")->all();
		} else if (!empty($data[$form->model]['blocks'])) {
			$containers = array();
			foreach ($data[$form->model]['blocks'] as $key => $content) {
				list($region, $position) = explode("-", $key);
				$containers[] = array("region" => $region, "position" => $position, "content" => $content, "type" => "text");
			}
		}
		$field['nolabel'] = true;
		$field['class'] = "rich-text";
		$field['style'] = "width:100%;height:100px";
		$field['containers'] = $containers;
	}
}
?>
