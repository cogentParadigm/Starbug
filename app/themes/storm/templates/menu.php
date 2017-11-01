<?php
	if (empty($attributes)) $attributes = array();
	$attributes["data-dojo-type"] = "storm/menu";
	$links = $this->collections->get("Menu")->query(["menu" => $menu]);
?>
<ul<?php echo $this->filter->attributes($attributes); ?>>
	<?php
		foreach ($links as $link) {
			$this->render("menu-link", array("link" => $link));
		}
	?>
</ul>
