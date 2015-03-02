<?php
	$attributes = $class = $link_attributes = array();
	$link_text = "";
	$active = false;

	//add first class for first item in list
	if ($link['position'] == 0) $class[] = "first";

	//determine where the link is going to
	if (!empty($link['uris_id'])) {
		$link_text = empty($link['breadcrumb']) ? $link['title'] : $link['breadcrumb'];
		$link_attributes['href'] = uri($link["path"]);
		$parts = explode("/", $link['path']);
		$active = !empty($link['path']);
		foreach ($parts as $idx => $part) if (request()->uri[$idx] !== $part) $active = false;
		if ($active) $class[] = "active";
	}

	//taxonomy menus
	if (!empty($link['term'])) $link['content'] = $link['term'];

	//href override
	if (!empty($link['href'])) {
		$absolute = (0 === strpos($link['href'], "http"));
		$link_attributes['href'] = $absolute ? $link['href'] : uri($link['href']);
		if (!$absolute) {
			$parts = explode("/", $link['href']);
			$active = true;
			foreach ($parts as $idx => $part) if (request()->uri[$idx] !== $part) $active = false;
			if ($active) $class[] = "active";
		}
	}

	//content override
	if (!empty($link['content'])) $link_text = $link['content'];

	//set the link target
	if (!empty($link['target'])) $link_attributes['target'] = $link['target'];

	//if there are children, we need to build a dropdown
	if (!empty($link['children'])) {
		$class[] = "dropdown";
		$class[] = "clearfix";
		if ($active) {
			$class[] = "open";
			unset($link_attributes['href']);
		} else {
			$link_attributes['data-toggle'] = "dropdown";
			$link_attributes['role'] = "button";
			$link_attributes['data-target'] = "#";
		}
		$link_attributes['class'] = "dropdown-toggle";
		$link_text .= '<i class="fa chevron pull-right"></i>';
	}

	//if sortable, set draggable attribute
	if ($sortable) {
		$attributes['draggable'] = "true";
		$link_attributes['href'] = "javascript:;";
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
			<div class="btn-group pull-right" style="position:relative;z-index:100">
				<a href="<?php echo uri("admin/$area/update/".$link['id']); ?>" class="btn btn-default Edit"><div class="fa fa-edit"></div></a>
				<a href="javascript:(function(){sb.get('<?php echo $model; ?>').remove('<?php echo $link['id']; ?>').then(function(){window.location.reload();});return false;})()" class="btn btn-default Delete"><div class="fa fa-times"></div></a>
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
