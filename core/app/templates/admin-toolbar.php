<?php
$new_label = "New ".$this->models->get($model)->singular_label." <b class=\"fa fa-plus\"></b>";
$new_attrs = "class:btn btn-default";
?>

<div class="btn-group pull-right">
	<?php
		if ($dialog) link_to($new_label, "", $new_attrs."  href:javascript:(function(){".$model."_form.show();return false;})()");
		else link_to($new_label, $request->getPath()."/create?".http_build_query($request->getParameters(), '?', '&'), $new_attrs);
		link_to("Export CSV <b class=\"fa fa-file-text-o\"></b>", "", array("href" => "javascript:window.location.href = ".$model."_grid.collection._renderUrl().replace('json', 'csv');", "class" => "btn btn-default"));
	?>
</div>
<?php
	$this->displays->render("SearchForm", array("model" => $model));
?>
<br/>
