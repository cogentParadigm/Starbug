<?php echo '<?php'."\n"; ?>
	$this->assign("query", "<?= $model; ?>");
	$this->render("list");
<?php echo '?>'; ?>
