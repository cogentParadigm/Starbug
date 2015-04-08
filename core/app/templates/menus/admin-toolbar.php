<?php
$options = schema($model);
?>
<div class="pull-right">
		<?php
			link_to("Export CSV <b class=\"fa fa-file-text-o\"></b>", "", array("href" => "javascript:window.location.href = ".$model."_grid.store.last_query.replace('json', 'csv');", "class" => "btn btn-default"));
			link_to("New Menu <b class=\"fa fa-plus\"></b>", $request->path."/create?new=true", "class:btn btn-default");
		?>
</div>
<?php $this->render_display("form", $model, "search", array("method" => "get")); ?>
<br/>
