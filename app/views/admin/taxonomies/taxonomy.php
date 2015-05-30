<?php if (success("terms", "create")) { ?>
	<div class="alert alert-success">Term <?php echo (empty($_POST['terms']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Update Taxonomy '<?php echo $taxonomy; ?>'</strong></div>
	<div class="panel-body">
	<div class="clearfix">
		<p class="pull-right"><?php link_to("Add Term <b class=\"fa fa-plus\"></b>", "admin/taxonomies/create?taxonomy=".$taxonomy, "class:btn btn-default"); ?></p>
	</div>
	<?php $this->displays->render("TermsTreeGrid",  array("taxonomy" => $taxonomy)); ?>
	</div>
</div>
