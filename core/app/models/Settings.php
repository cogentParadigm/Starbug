<?php
/**
 * options model
 * @ingroup models
 */
class Settings {

	function update($settings) {
		foreach ($settings as $k => $v) $this->store(array("value" => $v), "name:$k");
	}

	function delete($setting) {
		return $this->remove('id:'.$setting['id']);
	}
	
	function display_form($display, $ops) {
		$settings = query("settings")->select("settings.*,category.term,category.slug")->sort("settings_category.term_path, settings_category.position");
		$_POST['settings'] = array();
		$last = "";
		foreach ($settings as $idx => $setting) {
			$_POST['settings'][$setting['name']] = $setting['value'];
			if ($setting['term'] != $last) {
				if ($idx > 0) {
					//button("Save");
					//echo '<br class="clearfix"/><br/>';
				}
				$last = $setting['term'];
				$display->layout->add($setting['slug']."-row  ".$setting['slug'].":div.col-xs-12");
				$display->layout->put($setting['slug'], 'h1#'.$setting['term'].'.well', $setting['term']);
			}
			$field = array($setting['name'], "input_type" => $setting['type'], "pane" => $setting['slug']);
			if (!empty($setting['label'])) $field['label'] = $setting['label'];
			if (!empty($setting['options'])) $field += json_decode($setting['options'], true);
			if ($setting['type'] == "textarea") $field['data-dojo-type'] = 'dijit/form/Textarea';
			else if ($setting['type'] == "checkbox") $field['value'] = 1;
			$display->add($field);
		}
	}

}
?>
