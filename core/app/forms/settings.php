<div class="left" style="width:300px;min-height:1px">
	<?php
		assign("attributes", array("id" => "settings-menu", "class" => "nav-tabs nav-stacked", "data-spy" => "affix", "data-dojo-props" => "offset:90", "data-dojo-type" => "bootstrap/Affix", "style" => "width:300px;top:10px"));
		assign("menu", "");
		assign("taxonomy", "settings_categories");
		render("menu");
	?>
</div>
<div class="left" style="margin-left:20px">
<?php
	js("dijit/form/Textarea");
	if (success("settings", "update")) echo '<div class="success">Settings updated successfully</div>';
	$settings = query("settings,terms", "select:settings.*,terms.term,terms.slug  join:left  orderby:terms.term_path, terms.position ASC");
	$_POST['settings'] = array();
	$last = "";
	open_form("model:settings  action:update", "class:options_form");
	foreach ($settings as $setting) {
		$_POST['settings'][$setting['name']] = $setting['value'];
		if ($setting['term'] != $last) {
			$last = $setting['term'];
			echo '<h1 id="'.$setting['term'].'">'.$setting['term'].'</h1>';
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
</div>
<br class="clearfix"/><br/>
<script type="text/javascript">
	require(["dojo/query", "put-selector/put"], function(query, put) {
		query('#settings-menu > li > a').forEach(function(node) {
			put(node, '[href="<?php echo uri("admin/settings#"); ?>'+node.innerText+'"]');
			put(node, 'i.icon-chevron-right[style="float:right;font-size:1.6em;line-height:1.4em"]');
		});
	});
</script>
