<?php
class hook_form_blocks extends FormHook {
	function __construct(Request $request) {
		$this->request = $request;
	}
	function build($form, &$control, &$field) {
		$containers = array(array("region" => "content", "position" => 1, "content" => "", "type" => "text"));
		$item_id = $form->get("id");
		if (!empty($item_id)) {
			$containers = query("blocks")->condition("uris_id", $form->get("uris_id"))->sort("position")->all();
		} else if (!empty($this->request->data[$form->model]['blocks'])) {
			$containers = array();
			foreach ($this->request->data[$form->model]['blocks'] as $key => $content) {
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
