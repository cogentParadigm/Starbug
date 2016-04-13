<?php if ($this->db->success("terms", "create")) { ?>
	<div class="alert alert-success">Term saved successfully</div>
<?php } ?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>Update Taxonomy '<?php echo $taxonomy; ?>'</strong></div>
	<div class="panel-body">
	<div class="clearfix">
		<p class="pull-right">
			<a class="btn btn-default" href="<?php echo $this->url->build("admin/taxonomies/create?taxonomy=".$taxonomy); ?>">
				Add Term <b class="fa fa-plus"></b>
			</a>
		</p>
	</div>
	<?php $this->displays->render("TermsTreeGrid", array("taxonomy" => $taxonomy)); ?>
	</div>
</div>
