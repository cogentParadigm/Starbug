<?php
	$attributes = $class = $link_attributes = array();
	$link_text = "";
	
	//add first class for first item in list
	if ($link['position'] == 0) $class[] = "first";
	
	//determine where the link is going to
	if (!empty($link['uris_id'])) {
		$link_text = empty($link['breadcrumb']) ? $link['title'] : $link['breadcrumb'];
		$link_attributes['href'] = uri($link["path"]);
		$parts = explode("/", $link['path']);
		$active = true;
		foreach ($parts as $idx => $part) if ($request->uri[$idx] != $part) $active = false;
		if ($active) $class[] = "active";
	}
	
	//taxonomy menus
	if (!empty($link['term'])) $link['content'] = $link['term'];
	
	//href override
	if (!empty($link['href'])) $link_attributes['href'] = (0 === strpos($link['href'], "http")) ? $link['href'] : uri($link['href']);
	
	//content override
	if (!empty($link['content'])) $link_text = $link['content'];
	
	//set the link target
	if (!empty($link['target'])) $link_attributes['target'] = $link['target'];

	//if there are children, we need to build a dropdown
	if (!empty($link['children'])) {
		$class[] = "dropdown";
		$attributes['data-dojo-type'] = "bootstrap/Dropdown";
		$link_attributes['class'] = "dropdown-toggle";
		$link_attributes['data-taggle'] = "dropdown";
		$link_attributes['role'] = "button";
	}
	
	//if sortable, set draggable attribute
	if ($sortable) {
		$attributes['draggable'] = "true";
		unset($link_attributes['href']);
	}
	$attributes['data-menu-id'] = $link['id'];
	$attributes['data-parent'] = $link['parent'];
	
	if (!empty($link['template'])) $class[] = $link['template'];
	
	//serialize the array of classes
	if (!empty($class)) $attributes['class'] = implode(" ", $class);
	
	if ($menu_type == "taxonomy") {
		$area = "taxonomies";
		$model = "terms";
	} else {
		$area = $model = "menus";
	}
?>
<?php if (empty($link['template'])) { ?>
	<li<?php html_attributes($attributes); ?>>
		<?php if ($editable) { ?>
			<div class="btn-group right">
				<a href="<?php echo uri("admin/$area/update/".$link['id']); ?>" class="btn btn-mini Edit"><div class="sprite icon"></div></a>
				<a href="javascript:(function(){sb.get('<?php echo $model; ?>').remove('<?php echo $link['id']; ?>').then(function(){window.location.reload();});return false;})()" class="btn btn-mini Delete"><div class="sprite icon"></div></a>
			</div>
		<?php } ?>
		<a<?php html_attributes($link_attributes); ?>><?php echo $link_text; ?></a>
		<?php if (!empty($link['children'])) { ?>
		<ul class="dropdown-menu" role="menu">
			<?php
				foreach ($link['children'] as $cid => $child) {
					assign("link", $child);
					render("menu-link");
				}
			?>
		</ul>
		<?php } ?>
	</li>
<?php } else { ?>
	<?php
		if ($link['template'] == "divider") {
			echo '<li'.html_attributes($attributes, false).'>';
			if ($editable) {
				?>
			<div class="btn-group right">
				<a href="<?php echo uri("admin/$area/update/".$link['id']); ?>" class="btn btn-mini Edit"><div class="sprite icon"></div></a>
				<a href="javascript:(function(){sb.get('<?php echo $model; ?>').remove('<?php echo $link['id']; ?>').then(function(){window.location.reload();});return false;})()" class="btn btn-mini Delete"><div class="sprite icon"></div></a>
			</div>
				<?php
			}
			echo '</li>';
		} else render($link['template']);
	?>
<?php } ?>
