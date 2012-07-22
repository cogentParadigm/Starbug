	<h1 class="heading"><a class="big round right create button" href="<?php echo uri($request->path."/create"); ?>">New Taxonomy</a>Taxonomies</h1>
	<br/>
<?php
	assign("model", "terms");
	assign("query", "admin");
	$columns = array(
		"Taxonomy" => "field:'taxonomy'  formatter:taxonomy_formatter",
		"Options" => "field:'taxonomy'  formatter:row_options  class:field-options",
	);
	assign("columns", $columns);
	render("grid");
?>
	<a class="big round create button" href="<?php echo uri($request->path."/create"); ?>">New Taxonomy</a>
	<script type="text/javascript">
			function row_options(taxonomy) {
				var text = '<a class="Edit button" href="<?php echo uri($request->path."/update/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a>';
				text += '<a class="Delete button" href="<?php echo uri($request->path."/delete/"); ?>'+taxonomy+'<?= $to; ?>"><div class="sprite icon"></div></a>';
				return text;
			}
			function taxonomy_formatter(taxonomy) {
				return '<span style="text-transform:capitalize">'+taxonomy.replace('_', ' ')+'</span>';
			}
	</script>
