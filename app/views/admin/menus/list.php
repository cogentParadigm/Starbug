<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="Menus">Menus</span></strong></div>
	<div class="panel-body">
<?php
	assign("model", "menus");
	assign("query", "admin");
	$columns = array(
		"Menu" => "field:'menu'  formatter:menu_formatter",
		"Options" => "field:'menu'  formatter:row_options  class:field-options",
	);
	assign("columns", $columns);
	$grid = capture("grid");
	render("menus/admin-toolbar");
	echo $grid;
?>
	</div>
</div>
	<script type="text/javascript">
			function row_options(taxonomy) {
				var text = '<div class="btn-group"><a class="Edit btn btn-default" href="<?php echo uri($request->path."/menu/"); ?>'+taxonomy+'<?= $to; ?>"><div class="fa fa-edit"></div></a>';
				text += '<a class="Delete btn btn-default" href="<?php echo uri($request->path."/delete/"); ?>'+taxonomy+'<?= $to; ?>"><div class="fa fa-times"></div></a></div>';
				return text;
			}
			function menu_formatter(menu) {
				return '<span>'+menu+'</span>';
			}
	</script>
