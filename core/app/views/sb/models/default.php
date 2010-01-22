<h2>Permits</h2>
<script type="text/javascript">
	dojo.require("dojo.fx");
	function showhide(item) {
		dojo.toggleClass(item, "hidden");
	}
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

?>
<?php
	include("core/db/Schemer.php");
	$schemer = new Schemer($sb->db);
	include("etc/schema.php");
?>
<ul id="models" class="lidls">
<?php foreach ($schemer->tables as $name => $fields) { ?>
	<li id="<?php echo $name; ?>">
		<h3>
			<a href="" class="title" onclick="showhide('<?php echo $name; ?>_model');return false;"><?php echo $name; ?></a>
		</h3>
		<div id="<?php echo $name; ?>_model" class="hidden" style="padding:5px">
			<div class="info tab">
				<h4 class="left">Permits:</h4>
				<div class="permit_options"><a href="" class="inline_button create_permit" style="">create permit</a></div>
				<div class="permitlist">
				<?php $permits = $sb->query("permits", "where:related_table='".P($name)."'"); foreach($permits as $permit) { ?>
					<div class="permit"><?php echo "<strong>$permit[priv_type] $permit[action]</strong> access for <strong>$permit[role] ".(($permit['who']) ? $permit['who'] : "")."</strong>".(($permit['priv_type'] == "object") ? " on <strong>".$permit['related_id']."</strong>" : ""); ?><a href="<?php echo uri("sb/models?action=delete_permit&id=$permit[id]"); ?>" onclick="return confirm('Are you sure you want to delete this permit?');">delete</a></div>
				<?php } ?>
				</div>
			</div>
		</div>
	</li>
<?php } ?>
</ul>
