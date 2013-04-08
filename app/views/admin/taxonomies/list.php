<h1 class="heading">Taxonomies</h1>
<?php
	assign("model", "terms");
	assign("query", "admin");
	$columns = array(
		"Taxonomy" => "field:'taxonomy'",
		"Options" => "field:'taxonomy'  formatter:row_options  class:field-options",
	);
	assign("columns", $columns);
	$grid = capture("grid");
	render("terms/admin-toolbar");
	echo $grid;
?>
	<script type="text/javascript">
			function row_options(taxonomy) {
				var text = '<div class="btn-group"><a class="Edit btn" href="<?php echo uri($request->path."/taxonomy/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a>';
				text += '<a class="Delete btn" href="<?php echo uri($request->path."/delete/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a></div>';
				return text;
			}
	</script>
