<?php
$options = schema($model);
?>
<div class="btn-toolbar">
		<?php
			render_form(array($model."/search", "search"));
			link_to("Export CSV", "", array("href" => "javascript:window.location.href = ".$model."_grid.store.last_query.replace('json', 'csv');", "class" => "btn"));
			link_to("New Taxonomy <b class=\"icon-plus\"></b>", $request->path."/new", "class:btn");
		?>
</div>
