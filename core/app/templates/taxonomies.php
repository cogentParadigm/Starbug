	<h1 class="heading"><a class="big round right create button" href="<?php echo uri($request->path."/create"); ?>">New Taxonomy</a>Taxonomies</h1>
	<br/>
<?php
	assign("query", "terms  select:DISTINCT taxonomy");
	$columns = array(
		"Taxonomy" => "field:taxonomy  width:auto  formatter:taxonomy_formatter",
		"Options" => "field:taxonomy  width:100  formatter:row_options",
	);
	assign("columns", $columns);
	render("grid");
?>
	<a class="big round create button" href="<?php echo uri($request->path."/create"); ?>">New Taxonomy</a>
	<script type="text/javascript">
			function row_options(data, rowIndex) {
				var text = '<a class="Edit button" href="<?php echo uri($request->path."/update/"); ?>'+data+'<?= $to; ?>"><div class="sprite icon"></div></a>';
				text += '<form method="post" onsubmit="return confirm(\'All terms in this taxonomy will be removed. Are you sure you want to delete this item?\');"><input type="hidden" name="action[terms]" value="delete_taxonomy"/><input type="hidden" name="terms[taxonomy]"	value="'+data+'"/><button class="negativ Delete" title="delete" type="submit"><div class="sprite icon"></div></button></form>';
				return text;
			}
			function taxonomy_formatter(data) {
				return '<span style="text-transform:capitalize">'+data.replace('_', ' ')+'</span>';
			}
	</script>
