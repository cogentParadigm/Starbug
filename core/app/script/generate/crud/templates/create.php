<?php echo '<?php'."\n"; ?>
	$this->assign("model", "<?php echo $model; ?>");
	$this->assign("uri", "<?php echo $prefix.$model; ?>");
	$this->render("create");
<?php echo '?>'; ?>
