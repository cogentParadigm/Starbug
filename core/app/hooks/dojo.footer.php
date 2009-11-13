<script type="text/javascript">
	dojo.require("dojo.behavior");
<?php include("core/app/public/js/sb.js"); ?>
	dojo.addOnLoad(function() {
		<?php global $dojo; if (!empty($dojo->behaviors)) { ?>
		dojo.behavior.add({
			<?php $queries = count($dojo->behaviors); $i = 0; foreach($dojo->behaviors as $query => $events) { $i++; ?>
			"<?php echo $query; ?>" : {<?php $pairs = count($events); $j = 0; foreach($events as $event => $actions) { $j++; ?> <?php echo $event; ?> : function(evt) {evt.preventDefault();<?php foreach($actions as $act) echo str_replace(array("\n", "\t"), "", $act); ?>}<?php if ($j < $pairs) echo ","; echo "\n"; ?><?php } ?> }<?php if ($i < $queries) echo ","; echo "\n"; ?>
			<?php } ?>
		});
		<?php } ?>
	});
</script>
