<?= '<?php'."\n"; ?>
	$this->assign("model", "<?= $model; ?>");
	$this->assign("uri", "<?= $prefix.$model; ?>");
	$this->render("create");
<?= '?>'; ?>
