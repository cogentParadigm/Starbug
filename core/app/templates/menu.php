<?php
	if (empty($attributes)) $attributes = array();
	if (!empty($attributes['class'])) $attributes['class'] .= " ";
	$attributes['class'] .= "nav";
	if ($sortable) $attributes['class'] .= " sortable";

	$links = $this->collections->get("Menu")->query(["menu" => $menu]);
?>
<ul<?php echo $this->filter->attributes($attributes); ?>>
	<?php
		foreach ($links as $link) {
			$this->render("menu-link", array("link" => $link));
		}
	?>
</ul>
