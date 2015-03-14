<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New Menu Item">New Menu Item</span></strong></div>
	<div class="panel-body">
<?php
	$this->assign("menu", $_GET['menu']);
	$this->render_form("menus");
?>
	</div>
</div>
