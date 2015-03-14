<?php echo '<?php'."\n"; ?>
	$id = end($request->uri);
	$this->assign("model", "<?php echo $model; ?>");
	$this->assign("id", $id);
	$this->assign("uri", "<?php echo $prefix.$model; ?>");
	$this->render("update");
<?php echo '?>'; ?>
