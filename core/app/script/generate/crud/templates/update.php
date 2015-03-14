<?php echo '<?php'."\n"; ?>
	$id = end($request->uri);
	$this->assign("model", "<?= $model; ?>");
	$this->assign("id", $id);
	$this->assign("uri", "<?= $prefix.$model; ?>");
	$this->render("update");
<?php echo '?>'; ?>
