<h1 class="heading">Menus</h1>
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
	<script type="text/javascript">
			function row_options(taxonomy) {
				var text = '<div class="btn-group"><a class="Edit btn" href="<?php echo uri($request->path."/menu/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a>';
				text += '<a class="Delete btn" href="<?php echo uri($request->path."/delete/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a></div>';
				return text;
			}
			function menu_formatter(menu) {
				return '<span>'+menu+'</span>';
			}
	</script>
