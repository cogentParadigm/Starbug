<h2>Model</h2>
<?php
include("core/app/nouns/include/toolnav.php");
include("core/public/js/models.php");
?>
<script type="text/javascript">
	dojo.require("dojo.fx");
	function showhide(item) {
		dojo.toggleClass(item, "hidden");
	}
	
	function showtab(model, tabname) {
		dojo.query("#"+model+"_model .tab").addClass("hidden");
		dojo.query("#"+model+"_model .tab."+tabname).removeClass("hidden");
		dojo.query("#"+model+"_model .active.button").removeClass("active");
		dojo.query("#"+model+"_model .button."+tabname).addClass("active");
		var tab_bg = dojo.query("#"+model+"_model .tableft")[0];
		var tab_box = dojo.query("#"+model+"_model .tabright")[0];
		var active_button = dojo.query("#"+model+"_model .active.button")[0];
		var active_coords = dojo.coords(active_button);
		console.log(active_coords);
		var bg_move = dojo.animateProperty({node: tab_bg, properties: { left: {end: (active_coords.x-388), unit: "px"}}, duration: 250});
		dojo.fx.combine([bg_move, dojo.animateProperty({node: tab_box, properties: { width: {end: (active_coords.w-17), unit: "px"}}, duration: 250})]).play();
	}
	function init_tabs() {
		dojo.query(".tabs").forEach(function(item) {
			var tab_bg = dojo.query(".tableft", item)[0];
			var tab_box = dojo.query(".tabright", item)[0];
			var active_button = dojo.query(".active.button", item)[0];
			var active_coords = dojo.coords(active_button);
			console.log(active_coords);
			dojo.style(tab_bg, "left", (813+active_coords.l)+"px");
			dojo.style(tab_box, "width", (12+active_coords.w)+"px");
		});
	}
	dojo.addOnLoad(init_tabs);
</script>
<?php
	include("core/app/models/Models.php");
	$models_object = new Models($sb->db);
	$models = $models_object->get_all();
?>
<ul id="models" class="lidls">
<?php foreach ($models as $name => $fields) { $has = $models_object->is_active($name); $backup = file_exists("app/models/.".ucwords($name).".php");?>
	<li id="<?php echo $name; ?>"<?php if (!$has) echo " class=\"inactive\""; ?>>
		<h3>
			<a href="" onclick="delete_model('<?php echo $name; ?>');return false;">[X]</a>
			<?php if ($has) { ?>
				<a href="" onclick="deactivate_model('<?php echo $name; ?>');return false;">[deactivate]</a>
				<form class="hidden" id="deactivate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="deactivate_model" value="1" />
				</form>
			<?php } else { ?>
				<form class="hidden" id="activate_<?php echo $name; ?>" method="post">
					<input type="hidden" name="activate_model" value="1" />
					<input id="restore_backup" type="hidden" name="restore_backup" value="<?php echo $backup; ?>"/>
				</form>
				<a href="" onclick="activate_model('<?php echo $name; ?>', '<?php echo $backup; ?>');return false;">[activate]</a>
			<?php } ?>
			<a href="" class="title" onclick="showhide('<?php echo $name; ?>_model');return false;"><?php echo $name; ?></a>
		</h3>
		<div id="<?php echo $name; ?>_model" class="hidden" style="padding:5px">
			<div class="tabs">
				<span class="tableft"><span class="tabright"></span></span>
				<a class="fields button" href="" onclick="showtab('<?php echo $name; ?>', 'fields');return false;">fields</a>
				<a class="active info button" href="" onclick="showtab('<?php echo $name; ?>', 'info');return false;">info</a>
			</div>
			<div class="info tab">
				<?php $info = unserialize(file_get_contents("var/schema/.info/$name")); ?>
				<h4 class="left">Label</h4>
				<span class="right note"><strong>Note:</strong> Labels are used as a display value.</span>
				<p><?php echo $info['label']; ?></p>
			</div>
			<div class="hidden fields tab">
				<?php echo Models::dlfields($fields, $name, $has); ?>
				<a href="" class="button clear" onclick="new_field('<?php echo $name; ?>');return false;">new field</a>
			</div>
		</div>
	</li>
<?php } ?>
</ul>
<a href="models/create" onclick="new_model();return false;" class="button">new model</a>
