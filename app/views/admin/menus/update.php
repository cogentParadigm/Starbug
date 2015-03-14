<?php
	$record = get("menus", $id);
	efault($_POST['menus'], array());
	foreach ($record as $k => $v) efault($_POST['menus'][$k], $v);
	$menu = $record['menu'];
	$this->assign("menu", $menu);
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="Update Menu Item">Update Menu Item</span></strong></div>
	<div class="panel-body">
<?php
 $this->render_form("menus");
?>
	</div>
</div>
