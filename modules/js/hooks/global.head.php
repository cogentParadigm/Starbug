	<?php if (Etc::ENVIRONMENT == "production") { ?>
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
			<script type="text/javascript" src="<?php echo uri("core/app/public/js/dojo/release/dojo/dojo/dojo.js"); ?>" data-dojo-config="parseOnLoad: true, serverTime:'<?php echo date("Y-m-d H:i:s"); ?>'"></script>
	<?php } else { ?>
			<?php
				$profile = file_get_contents(BASE_DIR."/etc/dojo.profile.js");
				$profile = explode("\n", substr($profile, 15));
				foreach ($profile as $i => $p) if (0 === strpos(trim($p), "//")) unset($profile[$i]);
				$profile = str_replace(array("dependencies:", "layers:", "name:", "prefixes:", "'"), array('"dependencies":', '"layers":', '"name":', '"prefixes":', '"'), implode(" ", $profile));
				$profile = json_decode($profile, true);
				$paths = "";
				foreach ($profile['prefixes'] as $p) $paths .= "'$p[0]':'$p[1]', ";
			?>
			<script type="text/javascript" src="<?php echo uri("core/app/public/js/dojo/dojo/dojo.js"); ?>" data-dojo-config="async: true, parseOnLoad: true, serverTime:'<?php echo date("Y-m-d H:i:s"); ?>', isDebug:true"></script>
			<script type="text/javascript">
				require({
						packages: [<?php foreach ($profile['prefixes'] as $idx => $p) { if ($idx > 0) echo ','; echo "\n"; ?>
								{ name: '<?php echo $p[0]; ?>', location: '<?php echo $p[1]; ?>' }<?php } echo "\n"; ?>
						]
				}, [
					<?php foreach ($profile['layers'] as $lidx => $l) { foreach ($l['dependencies'] as $didx => $d) { if ($lidx > 0 || $didx > 0) echo ",\n"; ?>
					'<?php echo str_replace(".", "/", $d); ?>'<?php } } echo "\n"; ?>
				]);
				define.amd.jQuery = true;
			</script>
	<?php } ?>
	<?php
		$scripts = theme("scripts", $request->theme);
		efault($scripts, array());
	?>
	<?php foreach ($scripts as $script) { ?>
		<script type="text/javascript" src="<?php echo uri("app/themes/".$request->theme."/public/js/$script"); ?>"></script>	
	<?php } ?>
	<script type="text/javascript">
		var WEBSITE_URL = '<?php echo uri(); ?>';
	</script>
