<?php
	js("dijit/form/Textarea");
	if (success("settings", "update")) echo '<div class="success">Settings updated successfully</div>';
	$settings = query("settings,terms", "select:settings.*,terms.term,terms.slug  join:left  orderby:terms.position ASC");
	$_POST['settings'] = array();
	$last = "";
	open_form("model:settings  action:update", "class:options_form");
	foreach ($settings as $setting) {
		$_POST['settings'][$setting['name']] = $setting['value'];
		if ($setting['term'] != $last) {
			$last = $setting['term'];
			echo '<h1>'.$setting['term'].'</h1>';
		}
		$field = array($setting['name']);
		$options = array();
		if (!empty($setting['label'])) $field['label'] = $setting['label'];
		if (!empty($setting['options'])) $options = json_decode($setting['options'], true);
		if ($setting['type'] == "textarea") $field['data-dojo-type'] = 'dijit.form.Textarea';
		else if ($setting['type'] == "checkbox") $field['value'] = 1;
		f($setting['type'], $field, $options);
	}
	button("Save");
	close_form();
?>
