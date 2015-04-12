<?php echo '<?php'."\n"; ?>
	$this->assign("query", "<?php echo $model; ?>");
	$this->render("list");
<?php echo '?>'; ?>
