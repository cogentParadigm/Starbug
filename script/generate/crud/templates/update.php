<?= '<?php'."\n"; ?>
	$id = end($request->uri);
	assign("model", "<?= $model; ?>");
	assign("id", $id);
	assign("uri", "<?= $prefix.$model; ?>");
	render("update");
<?= '?>'; ?>
	
