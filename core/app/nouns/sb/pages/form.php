<?php if ($action == "update") { ?>
<script type="text/javascript">
function edit_eip_text(args) {
	var editable = args.args.evt.target;
	if ((editable.firstChild != null) && (editable.firstChild.textContent == "Cancel")) editable.parentNode.innerHTML = '<?php echo $_POST['pages']['name']; ?>';
	else if ((editable.firstChild != null) && (editable.firstChild.textContent == "Save")) sb.xhr({ args : { url : '<?php echo uri("api/pages/get.json"); ?>', content: { 'action[pages]' : "change_name", "new_name" : editable.parentNode.firstChild.value, "old_name" : "<?php echo $_POST['pages']['name']; ?>" }, method: 'post', action: name_saved, handleAs: 'json', node: args.args.node} });
	else editable.innerHTML = '<input type="text" class="text" value="'+editable.innerHTML+'" name="'+editable.parentNode.id+'" /><a href="" class="save_editable">Save</a><a href="" class="cancel_editable">Cancel</a>';
}
function name_saved(args) {
	args.args.node.innerHTML = args.args.data.pages[0].name;
}
var update_link = true;
function title_onchange(args) {
	var title = args.args.node;
	var permalink = dojo.query("#permalink .editable")[0];
	console.log(update_link);
	if (update_link) permalink.innerHTML = title.value.replace(/ /g, '-').toLowerCase();
}
</script>
<?php
	$sb->import("util/dojo");
	global $dojo;
	$dojo->attach(".editable", "edit_eip_text", "node:evt.target.parentNode");
	//$dojo->attach("#title", "title_onchange", "node:evt.target", "onchange");
}
$templates = array(); $layouts = array(); $leafs = array(); $leaf_types = array("--Add a Leaf--" => "");
if (false !== ($handle = opendir("app/nouns/templates/"))) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
	closedir($handle);
}
if (false !== ($handle = opendir("app/nouns/leafs/"))) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $leaf_types[str_replace("_", " ", $file)] = $file;
	closedir($handle);
}
if (false !== ($handle = opendir("app/nouns/layouts/"))) {
	while (false !== ($file = readdir($handle))) {
		if ((strpos($file, ".") !== 0)) {
			$name = substr($file, 0, strpos($file, "."));
			$layouts[$name] = $name;
			$layout = file_get_contents("app/nouns/layouts/".$file);
			$start = strpos($layout, "* containers:")+13;
			$end = strpos($layout, "\n", $start);
			$leafs[$name] = explode(",", trim(substr($layout, $start, $end-$start)));
		}
	}
	closedir($handle);
}
efault($_POST['pages']['template'], "Page");
efault($_POST['pages']['layout'], "2-col-right");
efault($_POST['pages']['status'], 4);
?>
		<?php sb::load("core/app/plugins/jsforms"); ?>
<?php
	$sb->import("util/form");
	$f = new form("pages", "action:$action");
	echo $f->open('class="pages_form"');
?>
	<div class="field">
		<?php
			echo $f->text("title");
			if ($action == "update") {
				echo $f->tag("label	id:link-label	content:Permalink:");
				echo $f->tag("span	id:permalink	class:link-span	content:".uri("")."<span class=\"editable\">".(($_POST['pages']['name']) ? $_POST['pages']['name'] : ".." )."</span>");
			}
		?>
		<div class="infield">
			<?php
				echo $f->select("template", $templates);
				echo $f->select("layout", $layouts);
				echo $f->select("status", $this->statuses);
				echo $f->submit("class:big round left button	value:".ucwords($action));
			?>
		</div>
		<div class="left">
			<?php echo $f->text("name	label:Permalink	style:width:630px"); ?>
		</div>
	</div>
	<?php
	if ($action == "update") {
		$l = new form("new-leaf");
		$r = new form("remove-leaf");
		foreach($leafs[$_POST['pages']['layout']] as $container) { ?>
			<fieldset>
				<legend><?php echo $container; ?></legend>
				<?php
					echo $sb->get("pages")->fields($container, $_POST['pages']['name']);
					echo $l->select($container."	nolabel:true	class:left", $leaf_types);
					$leaves = $sb->query("leafs", "where:page='".$_POST['pages']['name']."' && container='$container' ORDER BY position ASC");
					$rm = array("--Remove a Leaf--" => "");
					foreach($leaves as $one) $rm[$one['position']." ".$one['leaf']] = $one['position']." ".$one['leaf'];
					echo $r->select($container."	nolabel:true	class:left", $rm);
					echo $f->submit("class:round right button	value:Update");
				?>
			</fieldset>
		<?php } ?>
	<?php } ?>
</form>
