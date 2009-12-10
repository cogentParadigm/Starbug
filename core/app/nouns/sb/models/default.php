<h2>Models</h2>
<a href="models/create" class="new_model button right">new model</a>
<script type="text/javascript">
	dojo.require("dojo.fx");
	function showhide(item) {
		dojo.toggleClass(item, "hidden");
	}
	function showtab(model, tabname, xpos) {
		dojo.query("#"+model+"_model .tab").addClass("hidden");
		dojo.query("#"+model+"_model .tab."+tabname).removeClass("hidden");
		dojo.query("#"+model+"_model .active.button").removeClass("active");
		dojo.query("#"+model+"_model .button."+tabname).addClass("active");
		var tab_bg = dojo.query("#"+model+"_model .tableft")[0];
		var tab_box = dojo.query("#"+model+"_model .tabright")[0];
		var active_button = dojo.query("#"+model+"_model .active.button")[0];
		var active_coords = dojo.coords(active_button);
		var bg_move = dojo.animateProperty({node: tab_bg, properties: { left: {end: xpos, unit: "px"}}, duration: 250});
		dojo.fx.combine([bg_move, dojo.animateProperty({node: tab_box, properties: { width: {end: (active_coords.w-17), unit: "px"}}, duration: 250})]).play();
	}
	function init_tabs() {
		dojo.query(".tabs").forEach(function(item) {
			var tab_bg = dojo.query(".tableft", item)[0];
			var tab_box = dojo.query(".tabright", item)[0];
			var active_button = dojo.query(".active.button", item)[0];
			var active_coords = dojo.coords(active_button);
			dojo.style(tab_bg, "left", 813+"px");
			dojo.style(tab_box, "width", (12+active_coords.w)+"px");
		});
	}
	dojo.addOnLoad(init_tabs);
	function who_options() {
		var role = dojo.byId("role");
		if (role.selectedIndex == '1') sb.xhr({ args : { url : '<?php echo uri("sb/xhr/permits/who/user"); ?>', action: sb.replace, node: '#who' } });
		else if (role.selectedIndex == '2') sb.xhr({ args : { url : '<?php echo uri("sb/xhr/permits/who/group"); ?>', action: sb.replace, node: '#who' } });
		else sb.replace({args : { node:'#who', data : '<option value="0" selected="selected">n/a</option>'} });;
	}
	function id_options() {
		var priv_type = dojo.byId("priv_type");
		var priv_type = priv_type.options[priv_type.selectedIndex].value;
		if (priv_type == "object") dojo.removeClass(dojo.byId("related_id").parentNode, "hidden");
		else dojo.addClass(dojo.byId("related_id").parentNode, "hidden");
	}
	function create_permit(args) {
		sb.prepend(args);
		who_options();
		id_options();
	}
	function permit_created(args) {
		//console.log(args.args.data.permits);
		permit = args.args.data.permits[0];
		var permit_str = '<strong>'+permit.priv_type+' '+permit.action+'</strong> access for <strong>'+permit.role;
		if ((permit.role == 'user') || (permit.role = 'group')) if (permit.who != 0) permit_str += ' '+permit.who;
		permit_str += '</strong>';
		if (permit.priv_type == 'object') permit_str += 'on <strong>'+permit.related_id+'</strong>';
		args.args.node.innerHTML = permit_str;
	}
</script>
<?php
if (($_GET['action'] == 'delete_permit') && (is_numeric($_GET['id']))) $sb->remove("permits", "id='$_GET[id]'");
$sb->import("util/dojo");
global $dojo;
$dojo->xhr(".create_permit", "create_permit", "'sb/xhr/permits/form/'+mod.substr(0, mod.length-6)", "pre:var mod = evt.target.parentNode.parentNode.parentNode.id;console.log(mod);	node:'#'+mod+' .permitlist'");
$dojo->attach("#role", "who_options", "", "onchange");
$dojo->attach("#priv_type", "id_options", "", "onchange");
$dojo->attach(".cancel_permit", "sb.destroy", "node:evt.target.parentNode.parentNode");
$dojo->xhr(".save_permit", "permit_created", "'api/permits/get.json'", "form:evt.target.parentNode	node:evt.target.parentNode.parentNode	handleAs:'json'");
//model
$dojo->xhr(".new_model", "sb.append", "'sb/xhr/models/new/model'", "node:'#models'");
$dojo->attach(".cancel_new_model", "sb.destroy", "node:evt.target.parentNode.parentNode");
$dojo->xhr(".save_new_model", "sb.append", "'sb/xhr/models/get/model'", "form:'new_model_form'	node:'#models'");
$dojo->attach(".save_new_model", "sb.destroy", "node:'#'+evt.target.parentNode.parentNode.id");
$dojo->xhr(".delete_model", "sb.destroy", "'sb/xhr/models/remove/'+loc", "pre:var loc = evt.parentNode.parentNode.id;	node:'#'+loc	confirm:'Are you sure you want to delete this model?'");
//field
$dojo->xhr(".new_field", "sb.append", "'sb/xhr/models/new/field/'+loc.substr(0, loc.length-7)", "pre:var loc = evt.target.previousSibling.previousSibling.id;	node:'#'+loc");
$dojo->attach(".cancel_new_field", "sb.destroy", "node:'#'+evt.target.parentNode.id");
$dojo->xhr(".save_new_field", "sb.append", "'sb/xhr/models/get/field'", "form:'new_field_form'	node:evt.target.parentNode.parentNode");
$dojo->attach(".save_new_field", "sb.destroy", "node:evt.target.parentNode");
$dojo->xhr(".edit_field", "sb.replace", "'sb/xhr/models/edit/field/'+loc", "pre:var loc = evt.target.parentNode.nextSibling.id;	node:'#'+loc+'-key'");
$dojo->xhr(".cancel_edit_field", "sb.replace", "'sb/xhr/models/get/field/'+loc", "pre:var loc = evt.target.parentNode.nextSibling.id;	node:'#'+loc+'-key'");
$dojo->xhr(".save_edit_field", "sb.replace", "'sb/xhr/models/get/field/'+loc", "node:'#'+loc+'-key'	form:'edit_field_form'	pre:var loc = evt.target.parentNode.nextSibling.id;");
$dojo->xhr(".delete_field", "sb.destroy", "'sb/xhr/models/remove/'+loc", "pre:var loc = evt.target.parentNode.nextSibling.id;	node:['#'+loc, '#'+loc+'-key']	confirm:'Are you sure you want to delete this field?'");
//key
$dojo->xhr(".new_key", "sb.append", "'sb/xhr/models/new/key/'+loc", "node:'#'+loc	pre:var loc = evt.target.parentNode.nextSibling.id;");
$dojo->attach(".cancel_new_key", "sb.destroy", "node:evt.target.parentNode");
$dojo->xhr(".save_new_key", "sb.append", "'sb/xhr/models/get/key'", "node:'#'+evt.target.parentNode.parentNode.firstChild.id	method:'post'	form:'new_key_form'");
$dojo->attach(".save_new_key", "sb.destroy", "node:'#'+evt.target.parentNode.id");
$dojo->xhr(".edit_key", "sb.replace", "'sb/xhr/models/edit/key/'+loc", "node:'#'+loc	pre:var loc = evt.target.parentNode.id;loc = loc.substr(0, loc.length-8);");
$dojo->xhr(".cancel_edit_key", "sb.replace", "'sb/xhr/models/get/key/'+loc", "node:'#'+loc	pre:var loc = evt.target.parentNode.parentNode.id;");
$dojo->xhr(".save_edit_key", "sb.replace", "'sb/xhr/models/get/key/'+loc", "pre:var loc = evt.target.parentNode.parentNode.id;	node:'#'+loc	method:'post'	form:'edit_key_form'");
$dojo->xhr(".delete_key", "sb.destroy", "'sb/xhr/models/remove/'+loc", "pre:var loc = evt.target.parentNode.parentNode.id;	node:['#'+loc, '#'+loc+'-key']	confirm:'Are you sure you want to delete this key?'");

include("core/app/nouns/include/toolnav.php");
include("core/app/public/js/models.php");
?>
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
				<a class="fields button" href="" onclick="showtab('<?php echo $name; ?>', 'fields', 848);return false;">fields</a>
				<a class="active info button" href="" onclick="showtab('<?php echo $name; ?>', 'info', 812);return false;">info</a>
			</div>
			<div class="info tab">
				<?php $info = unserialize(file_get_contents("var/schema/.info/$name")); ?>
				<h4 class="left">Label:</h4>
				<span class="right note"><strong>Note:</strong> Labels are used as a display value.</span>
				<span class="field-value"><?php echo $info['label']; ?></span>
				<h4 class="left">Permits:</h4>
				<div class="permit_options"><a href="" class="inline_button create_permit" style="">create permit</a></div>
				<div class="permitlist">
				<?php $permits = $sb->query("permits", "where:related_table='".P($name)."'"); foreach($permits as $permit) { ?>
					<div class="permit"><?php echo "<strong>$permit[priv_type] $permit[action]</strong> access for <strong>$permit[role] ".(($permit['who']) ? $permit['who'] : "")."</strong>".(($permit['priv_type'] == "object") ? " on <strong>".$permit['related_id']."</strong>" : ""); ?><a style="padding-right:10px;border-right:1px solid;margin:0 10px" class="edit_permit" href="">change</a><a href="<?php echo uri("sb/models?action=delete_permit&id=$permit[id]"); ?>" onclick="return confirm('Are you sure you want to delete this permit?');">delete</a></div>
				<?php } ?>
				</div>
			</div>
			<div class="hidden fields tab">
				<?php echo Models::dlfields($fields, $name, $has); ?>
				<a href="" class="button clear new_field">new field</a>
			</div>
		</div>
	</li>
<?php } ?>
</ul>
