<?php
	if (success("options", "create")) echo '<div class="success">Options updated successfully</div>';
	$options = query("options");
	$_POST['options'] = array();
	foreach ($options as $option) $_POST['options'][$option['name']] = $option['value'];
	open_form("model:options  action:create", "class:options_form");
	echo '<h1>SEO</h1>';
	js("dijit/form/Textarea");
	textarea("meta  label:Custom Analytics, etc..  data-dojo-type:dijit.form.Textarea");
	checkbox("seo_hide  label:Hide from search engines  value:1");
	button("Save");
	close_form();
?>
