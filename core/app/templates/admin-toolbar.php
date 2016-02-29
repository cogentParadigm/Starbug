<?php
$new_label = "New ".$this->models->get($model)->singular_label." <b class=\"fa fa-plus\"></b>";
$new_attrs = "class:btn btn-default";
?>

<div class="btn-group pull-right">
	<?php
		$path = $this->url->build($this->request->getPath()."/create");
		if ($dialog) $path = "javascript:(function(){".$model."_form.show;return false;})()";
	?>
	<a class="btn btn-default" href="<?php echo $path; ?>"><?php echo $new_label; ?></a>
	<a class="btn btn-default" href="javascript:window.location.href = <?php echo $model; ?>_grid.collection._renderUrl().replace('json', 'csv');">
		Export CSV <b class="fa fa-file-text-o"></b>
	</a>
</div>
<?php
	$this->displays->render("SearchForm", array("model" => $model));
?>
<br/>
