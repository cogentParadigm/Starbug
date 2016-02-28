<?php
namespace Starbug\Core;
class SettingsForm extends FormDisplay {
	public $model = "settings";
	public $cancel_url = "admin";
	public $operation = "update";
	function build_display($options) {
		$settings = $this->models->get("settings")->query()->select("settings.*,category.term,category.slug")->sort("settings_category.term_path, settings_category.position");
		$this->request->setPost('settings', array());
		$last = "";
		foreach ($settings as $idx => $setting) {
			$this->request->setPost('settings', $setting['name'], $setting['value']);
			if ($setting['term'] != $last) {
				if ($idx > 0) {
					//button("Save");
					//echo '<br class="clearfix"/><br/>';
				}
				$last = $setting['term'];
				$this->layout->add($setting['slug']."-row  ".$setting['slug'].":div.col-xs-12");
				$this->layout->put($setting['slug'], 'h1#'.$setting['term'].'.well', $setting['term']);
			}
			$field = array($setting['name'], "input_type" => $setting['type'], "pane" => $setting['slug']);
			if (!empty($setting['label'])) $field['label'] = $setting['label'];
			if (!empty($setting['options'])) $field += json_decode($setting['options'], true);
			if ($setting['type'] == "textarea") $field['data-dojo-type'] = 'dijit/form/Textarea';
			else if ($setting['type'] == "checkbox") $field['value'] = 1;
			$this->add($field);
		}
	}
}
?>
