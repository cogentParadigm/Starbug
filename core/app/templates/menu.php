<?php
	if (!empty($menu)) {
		$menu_type = "menu";
		$mpath = "menu_path";
	} else if (!empty($taxonomy)) {
		$menu_type = "taxonomy";
		$mpath = "term_path";
	}
	assign("menu_type", $menu_type);

	efault($attributes, array());
	$attributes['class'] = (empty($attributes['class']) ? "" : $attributes['class']." ")."nav";
	
	if ($sortable) $attributes['class'] .= " sortable";
	
	if ($menu_type == "taxonomy") $records = query("terms", "where:terms.taxonomy=?  orderby:terms.term_path ASC, terms.position ASC", array($taxonomy));
	else $records = query("menus,uris", "select:menus.*,uris.title,uris.path,uris.breadcrumb  join:left  where:menus.menu=?  orderby:menus.menu_path ASC, menus.position ASC", array($menu));
	$links = array();
	
	$forbidden = array();
	foreach ($records as $link) {
		if ($forbidden[$link['parent']] || (!empty($link['collective']) && !(userinfo("memberships") & $link['collective']))) {
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
			assign("link", $link);
			render("menu-link");
		}
	?>
</ul>
