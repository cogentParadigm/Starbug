<?php
	$records = query("terms")
							->condition("taxonomy", $taxonomy)
							->sort("terms.term_path")->sort("terms.position")->all();
	$links = array();

	foreach ($records as $link) {
		$link['children'] = array();
		if ($link['parent'] == 0) $links[$link['id']] = $link;
		else {
			$chain = explode("-", trim($link['term_path'], "-"));
			$parent = &$links[array_shift($chain)];
			foreach ($chain as $c) $parent = &$parent['children'][$c];
			$parent['children'][$link['id']] = $link;
		}
	}

	function term_option($link, $prefix="") {
		$selected = ($_POST['terms']['parent'] == $link['id']) ? ' selected="selected"' : '';
		echo '<option value="'.$link['id'].'"'.$selected.'>'.$prefix.$link['term'].'</option>';
		foreach ($link['children'] as $child) menu_option($child, $prefix."-");
	}

?>
<?php if (success("terms", "create")) { ?>
	<div class="alert alert-success">Term <?= (empty($_POST['terms']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
	<?php
		open_form("model:terms  action:create", "class:terms-form");
		hidden("taxonomy  default:".$taxonomy);
	?>
	<div class="form-group select">
		<label for="parent_select">Parent</label>
		<select id="parent_select" name="terms[parent]" class="form-control">
				<option value="0"></option>
				<?php foreach ($links as $link) term_option($link) ?>
		</select>
		<span class="info">Leave empty to place the item at the top level.</span>
	</div>
	<?php //text("position  info:Enter 0 for first position, leave empty for last."); ?>
	<?php text("term"); ?>
	<?php textarea("description"); ?>
	<br/>
	<div class="btn-group"><button class="submit btn btn-success" type="submit">Save</button><button type="button" class="cancel btn btn-danger" onclick="window.location='<?= uri("admin/taxonomies/taxonomy/".$taxonomy); ?>'">Cancel</button></div>
	<?php close_form(); ?>
	<br class="clear"/>
