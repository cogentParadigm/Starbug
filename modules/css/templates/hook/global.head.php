<?php echo implode("\n", $this->css->getStylesheets()); ?>
<?php if ($this->css->hasLess()) { ?>

<script type="text/javascript">
		less = { env: 'development' };
</script>
<script src="<?php echo $this->url->build("libraries/less/dist/less.min.js"); ?>" type="text/javascript"></script>
<?php } ?>
