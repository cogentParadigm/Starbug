	<script type="text/javascript">
		var dojoConfig = {
			async:true,
			parseOnLoad:true,
			serverTime:'<?php echo date("Y-m-d H:i:s"); ?>'
		};
	</script>
<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php if ($this->dojo->getEnvironment() == "production") { ?>

	<script type="text/javascript" src="<?php echo $this->url->build("libraries/release/dojo/dojo/dojo.js"); ?>"></script>
	<?php $response->js("dojo/parser"); ?>

<?php } else { ?>

	<script type="text/javascript" src="<?php echo $this->url->build("libraries/dojo/dojo.js"); ?>"></script>
	<script type="text/javascript">
		require({
			packages: <?php echo $this->dojo->getPackages()."\n"; ?>
		},
			<?php echo $this->dojo->getDependencies()."\n"; ?>
		);
		define.amd.jQuery = true;
	</script>

<?php } ?>

<?php
	$scripts = $this->config->get("info.scripts", 'themes/'.$request->theme);
	if (empty($scripts)) $scripts = array();
?>
<?php foreach ($scripts as $script) { ?>
	<script type="text/javascript" src="<?php echo $this->url->build("app/themes/".$request->theme."/public/js/$script"); ?>"></script>
<?php } ?>
	<script type="text/javascript">
		var WEBSITE_URL = '<?php echo $this->url->build(""); ?>';
	</script>
