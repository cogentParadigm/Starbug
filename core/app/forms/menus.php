<?php
	$records = query("menus<uris")->select("menus.*,uris.title,uris.path,uris.breadcrumb")
							->condition("menus.menu", $menu)->condition("menus.statuses.slug", "deleted", "!=", array("ornull" => true))
							->sort("menus.menu_path")->sort("menus.position")->all();
	$links = array();

	foreach ($records as $link) {
		$link['children'] = array();
		if ($link['parent'] == 0) $links[$link['id']] = $link;
		else {
			$chain = explode("-", trim($link['menu_path'], "-"));
			$parent = &$links[array_shift($chain)];
			foreach ($chain as $c) $parent = &$parent['children'][$c];
			$parent['children'][$link['id']] = $link;
		}
	}

	function menu_option($link, $prefix="") {
		$selected = ($_POST['menus']['parent'] == $link['id']) ? ' selected="selected"' : '';
		echo '<option value="'.$link['id'].'"'.$selected.'>'.$prefix.(empty($link['content']) ? $link['title'] : $link['content']).'</option>';
		foreach ($link['children'] as $child) menu_option($child, $prefix."-");
	}

?>
<?php if (success("menus", "create")) { ?>
	<div class="success">Menu <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
	<?php
		open_form("model:menus  action:create", "class:menu-form");
		if (!empty($_GET['new'])) text("menu");
		else hidden("menu  default:".$_GET['menu']);
	?>
	<div class="row">
		<div class="form-group select col-md-6">
			<label>Parent</label>
			<select id="parent_select" name="menus[parent]" class="form-control">
					<option value="0"></option>
					<?php foreach ($links as $link) menu_option($link) ?>
			</select>
			<span class="help-block">Leave empty to place the item at the top level.</span>
		</div>
		<?php text("position  div:col-md-6  info:Enter 1 for first position, leave empty for last."); ?>
	</div>
	<div class="row">
	<?php
		//select("type  options:Link to page,Custom link,Divider  values:page,link,divider  onchange:this.parentNode.parentNode.className = 'menu_form '+this.options[this.selectedIndex].value");
		select("uris_id  div:col-md-6  label:Page  from:uris  caption:%title%  value:id  info:Select a page.", array("" => "NULL"));
		text("href  div:col-md-6  label:URL  info:Enter a URL manually.");
	?>
	</div>
	<div class="row">
		<?php text("content  div:col-md-6  id:content-field  info:Override the link text."); ?>
		<div class="col-md-6">
			<?php
				checkbox("target  label:Open in new tab/window  value:_blank");
				checkbox("template  label:Divider  value:divider  div:checkbox");
			?>
		</div>
	</div>
	<div class="btn-group"><button class="submit btn btn-success" type="submit">Save</button><button type="button" class="cancel btn btn-danger" onclick="window.location='<?= uri("admin/menus/menu/".$menu); ?>'">Cancel</button></div>
	<?php close_form(); ?>
	<br class="clear"/>
