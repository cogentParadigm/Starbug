<?php
$new_label = "New Import <b class=\"fa fa-plus\"></b>";
?>

<div class="btn-group pull-right">
	<?php
		$path = $this->url->build("admin/imports/create?model=".$model);
	?>
	<a class="btn btn-default" href="<?php echo $path; ?>"><?php echo $new_label; ?></a>
	<a class="btn btn-default" href="javascript:window.location.href = imports_grid.collection._renderUrl().replace('json', 'csv');">
		Export CSV <b class="fa fa-file-text-o"></b>
	</a>
</div>
<?php
	$this->displays->render("SearchForm", array("model" => "imports"));
?>
<br/>
