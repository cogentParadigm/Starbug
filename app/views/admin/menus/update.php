<?php
	$record = get("menus", $id);
	efault($_POST['menus'], array());
	foreach ($record as $k => $v) efault($_POST['menus'][$k], $v);
	$menu = $record['menu'];
	assign("menu", $menu);
?>
<h1>Update Menu Item</h1>
<?php
 render_form("menus");
?>
