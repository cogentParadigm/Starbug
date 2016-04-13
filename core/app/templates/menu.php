<?php
	if (!empty($menu)) {
		$menu_type = "menu";
		$mpath = "menu_path";
	} else if (!empty($taxonomy)) {
		$menu_type = "taxonomy";
		$mpath = "term_path";
	}

	if (empty($attributes)) $attributes = array();
	if (!empty($attributes['class'])) $attributes['class'] .= " ";
	$attributes['class'] .= "nav";

	if ($sortable) $attributes['class'] .= " sortable";

	if ($menu_type == "taxonomy") $records = $this->db->query("terms")->condition("terms.taxonomy", $taxonomy)->sort("terms.term_path ASC, terms.position ASC");
	else $records = $this->db->query("menus")->select("menus.*,menus.uris_id.title,menus.uris_id.path,menus.uris_id.breadcrumb")->condition("menus.menu", $menu)->sort("menus.menu_path ASC, menus.position ASC");
	$links = array();

	$forbidden = array();
	foreach ($records as $link) {
		if ($forbidden[$link['parent']]) {
			$forbidden[$link['id']] = true;
			continue;
		}
		$link['children'] = array();
		if ($link['parent'] == 0) $links[$link['id']] = $link;
		else {
			$chain = explode("-", trim($link[$mpath], "-"));
			$parent = &$links[array_shift($chain)];
			foreach ($chain as $c) $parent = &$parent['children'][$c];
			$parent['children'][$link['id']] = $link;
		}
	}
?>
<ul<?php html_attributes($attributes); ?>>
	<?php
		foreach ($links as $link) {
			$this->render("menu-link", array("link" => $link, "menu_type" => $menu_type));
		}
	?>
</ul>
