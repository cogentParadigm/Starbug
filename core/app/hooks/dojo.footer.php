<script type="text/javascript" src="<?php echo uri("core/app/public/js/sb.js"); ?>"></script>
<script type="text/javascript">
	dojo.require("dojo.behavior");
	<?php global $dojo; if (!empty($dojo->toggles)) { ?>dojo.require("dojo.fx");<?php } ?>
	<?php if ($dojo->dialogs) { ?>
	dojo.require("dijit.Dialog");
	<?php } ?>
	dojo.addOnLoad(function() {
		<?php foreach ($dojo->toggles as $toggle => $ops) { ?>
		var <?php echo $toggle; ?> = new dojo.fx.Toggler({node:'<?php echo $ops['node']; ?>'<?php if ($ops['add']) echo ", ".$ops['add']; ?>});
		var node = dojo.byId('<?php echo $ops['node']; ?>');
		node.setAttribute('displayed', '<?php echo $ops['default']; ?>');
		<?php echo $ops['toggler'].".".(($ops['default'] == "on") ? "show();" : "hide();"); ?>
		<?php } ?>
		<?php if (!empty($dojo->behaviors)) { ?>
		dojo.behavior.add({
			<?php $queries = count($dojo->behaviors); $i = 0; foreach($dojo->behaviors as $query => $events) { $i++; ?>
			"<?php echo $query; ?>" : {<?php $pairs = count($events); $j = 0; foreach($events as $event => $actions) { $j++; ?> <?php echo $event; ?> : function(evt) {evt.preventDefault();<?php foreach($actions as $act) echo str_replace(array("\n", "\t"), "", $act); ?>}<?php if ($j < $pairs) echo ","; echo "\n"; ?><?php } ?> }<?php if ($i < $queries) echo ","; echo "\n"; ?>
			<?php } ?>
		});
		dojo.behavior.apply();
		<?php } ?>
	});
</script>
