<?php
	$records = query("menus,uris", "select:menus.*,uris.title,uris.path,uris.breadcrumb  join:left  where:menus.menu=? && !(menus.status & 1)  orderby:menus.menu_path ASC, menus.position ASC", array($menu));
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
	<div class="inline-fields">
		<div class="input select">
			<label>Parent</label>
			<select id="parent_select" name="menus[parent]">
					<option value="0"></option>
					<?php foreach ($links as $link) menu_option($link) ?>
			</select>
			<span class="info">Leave empty to place the item at the top level.</span>
		</div>
		<?php text("position  info:Enter 1 for first position, leave empty for last."); ?>
	</div>
	<div class="inline-fields">
	<?php
		//select("type  options:Link to page,Custom link,Divider  values:page,link,divider  onchange:this.parentNode.parentNode.className = 'menu_form '+this.options[this.selectedIndex].value");
		select("uris_id  label:Page  from:uris  caption:%title%  value:id  info:Select a page.", array("" => "NULL"));
		text("href  label:URL  info:Enter a URL manually.");
	?>
	</div>
	<div class="inline-fields">
		<div class="input">
			<?php
				checkbox("target  label:Open in new tab/window  value:_blank  div:checkbox");
				echo "<br/>";
				checkbox("template  label:Divider  value:divider  div:checkbox");
			?>
		</div>
		<?php text("content  id:content-field  info:Override the link text."); ?>
	</div>
	<div class="btn-group"><button class="submit btn" type="submit">Save</button><button type="button" class="cancel btn" onclick="window.location='<?= uri("admin/menus/menu/".$menu); ?>'">Cancel</button></div>
	<?php close_form(); ?>	
	<br class="clear"/>
