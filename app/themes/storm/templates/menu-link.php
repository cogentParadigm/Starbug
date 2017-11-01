<?php
	$attributes = $class = $link_attributes = array();
	$active = false;

	//add first class for first item in list
	if ($link['position'] == 0) $class[] = "first";

	//href override
	if (!empty($link['href'])) {
		$absolute = (0 === strpos($link['href'], "http"));
		$link_attributes['href'] = $absolute ? $link['href'] : $this->url->build($link['href']);
		if (!$absolute) {
			$parts = explode("/", $link['href']);
			$active = true;
			foreach ($parts as $idx => $part) if ($request->getComponent($idx) !== $part) $active = false;
			if ($active) $class[] = "active";
		}
	}

	//set the link target
	if (!empty($link['target'])) $link_attributes['target'] = $link['target'];

	//if there are children, we need to build a dropdown
	if (!empty($link['children'])) {
		//$class[] = "dropdown";
		//$class[] = "clearfix";
		if ($active) {
			//$class[] = "open";
			//unset($link_attributes['href']);
		} else {
			//$link_attributes['data-toggle'] = "dropdown";
			//$link_attributes['role'] = "button";
			//$link_attributes['data-target'] = "#";
			//$response->js("bootstrap/Dropdown");
		}
		//$link_attributes['class'] = "dropdown-toggle";
		//$link["content"] .= '<i class="fa chevron pull-right"></i>';
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
?>
<?php if (empty($link['template'])) { ?>
	<li<?php echo $this->filter->attributes($attributes); ?>>
		<a<?php echo $this->filter->attributes($link_attributes); ?>>
		<?php if (!empty($link["icon"])) { ?>
			<i class="fa fa-lg fa-fw <?php echo $link["icon"]; ?>"></i>
		<?php } ?>
		<span class="menu-item-parent"><?php echo $link["content"]; ?></span>
		</a>
		<?php if (!empty($link['children'])) { ?>
		<ul style="display:none">
			<?php
				foreach ($link['children'] as $cid => $child) {
					$this->render("menu-link", array("link" => $child));
				}
			?>
		</ul>
		<?php } ?>
	</li>
<?php } else { ?>
	<?php
		if ($link['template'] == "divider") {
			echo '<li'.$this->filter->attributes($attributes).'>';
			echo '</li>';
		} else $this->render($link['template']);
	?>
<?php } ?>
