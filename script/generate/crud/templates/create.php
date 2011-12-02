<?= '<?php'."\n"; ?>
	assign("model", "<?= $model; ?>");
	assign("uri", "<?= $prefix.$model; ?>");
	render("create");
<?= '?>'; ?>
