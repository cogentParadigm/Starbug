<div class="pull-right">
	<?php
		$path = $this->url->build($this->request->getPath()."/create?new=true");
	?>
	<a class="btn btn-default" href="javascript:window.location.href = <?php echo $model; ?>_grid.collection._renderUrl().replace('json', 'csv');">
		Export CSV <b class="fa fa-file-text-o"></b>
	</a>
	<a class="btn btn-default" href="<?php echo $path; ?>">New Taxonomy <b class="fa fa-plus"></b></a>
</div>
<?php $this->displays->render("SearchForm", array("model" => $model)); ?>
<br/>
