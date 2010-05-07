<script type="text/javascript">
var old_link = '';
var old_title = '<?php if ($action == "update") echo $_POST['uris']['title']; ?>';
function edit_eip_text(args) {
	var editable = args.args.evt.target;
	if ((editable.firstChild != null) && (editable.firstChild.textContent == "Cancel")) {
		<?php if ($action == "update") { ?>
			editable.parentNode.innerHTML = '<?php echo $_POST['uris']['path']; ?>';
		<?php } else { ?>
			editable.parentNode.innerHTML = dojo.byId("path").value = old_link;
		<?php } ?>
	} else if ((editable.firstChild != null) && (editable.firstChild.textContent == "Save")) {
		<?php if ($action == "update") { ?>
			sb.xhr({ args : { url : '<?php echo uri("api/uris/get.json"); ?>', content: { 'action[uris]' : "change_name", "new_name" : editable.parentNode.firstChild.value, "old_name" : "<?php echo $_POST['uris']['path']; ?>" }, method: 'post', action: name_saved, handleAs: 'json', node: args.args.node} });
		<?php } else { ?>
			editable.parentNode.innerHTML = old_link = dojo.byId("path").value = editable.parentNode.firstChild.value;
		<?php } ?>
	} else editable.innerHTML = '<input type="text" class="text" value="'+editable.innerHTML+'" name="'+editable.parentNode.id+'" /><a href="" class="save_editable">Save</a><a href="" class="cancel_editable">Cancel</a>';
}
function name_saved(args) {
	args.args.node.innerHTML = old_link = dojo.byId("path").value = args.args.data.uris[0].path;
}
var update_link = true;
function title_onchange(args) {
	var title = args.args.node;
	var permalink = dojo.query("#permalink .editable")[0];
	var namebox = dojo.byId("path");
	if ((old_title.replace(/ /g, '-').toLowerCase()) == old_link) permalink.innerHTML = namebox.value = old_link = title.value.replace(/ /g, '-').toLowerCase();
	old_title = title.value;
}
function editable_onchange(args) {
	var editable = args.args.node;
	var textbox = dojo.byId('path');
	textbox.value = editable.innerHTML;
}
<?php if ($action == "update") { ?>
function apply_tags() {
	sb.xhr({
		args: {
			url: '<?php echo uri("api/uris.tags/get.json"); ?>',
			content: {
				'action[uris]': 'apply_tags',
				'uris[id]': '<?php echo $_POST['uris']['id']; ?>',
				'tags': dojo.byId('tagbox').value
			},
			method: 'post',
			handleAs: 'json',
			action: display_tags,
			node: dojo.byId('applied_tags')
		}
	});
}
function remove_tag(tag) {
	sb.xhr({
		args: {
			url: '<?php echo uri("api/uris.tags/get.json"); ?>',
			content: {
				'action[uris]': 'remove_tag',
				'uris[id]': '<?php echo $_POST['uris']['id']; ?>',
				'tag': tag
			},
			method: 'post',
			handleAs: 'json',
			action: display_tags,
			node: dojo.byId('applied_tags')
		}
	});
}
function display_tags(args) {
	console.log(args);
	var list = "";
	for(var i=0;i<args.args.data.uris.length;i++) {
		console.log(args.args.data.uris[i]);
		var item = args.args.data.uris[i];
		list += '<li><a href="javascript:remove_tag(\''+item.tag+'\');">x</a> '+item.tag+'</li>\n';
	}
	args.args.node.innerHTML = list;
}
<?php } ?>
</script>
<?php
	$sb->import("util/dojo");
	global $dojo;
	$dojo->attach(".editable", "edit_eip_text", "node:evt.target.parentNode");
	$dojo->attach("#title", "title_onchange", "node:evt.target", "onchange");
	$dojo->attach(".editable", "editable_onchange", "node:evt.target", "onchange");

	$collectives = array_merge(array("everybody" => 0), $this->groups);
	$parents = $sb->query("uris", "action:read");
	$kids = array();
	foreach($parents as $u) $kids[$u['parent']][] = $u;
	function parent_options($u, $k, $l=0) {
		$arr = array();
		$key = $u['path'];
		for($i=0;$i<$l;$i++) $key = "-".$key;
		$arr[$key] = $u['id'];
		if (!empty($k[$u['id']])) foreach ($k[$u['id']] as $kid) $arr = array_merge_recursive($arr, parent_options($kid, $k, $l+1));
		return $arr;
	}
	$parent_ops = array(" -- " => 0);
	foreach($kids[0] as $child) $parent_ops = array_merge_recursive($parent_ops, parent_options($child, $kids));

	$templates = array(); $containers = array(); $leaf_types = array("--Add a Leaf--" => "");
	if (false !== ($handle = opendir("app/views/templates/"))) {
		while (false !== ($file = readdir($handle))) if (((strpos($file, ".") !== 0)) && ($file != "options")) $templates[substr($file, 0, strpos($file, "."))] = substr($file, 0, strpos($file, "."));
		closedir($handle);
	}
	if (false !== ($handle = opendir("app/views/leafs/"))) {
		while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $leaf_types[str_replace("_", " ", $file)] = $file;
		closedir($handle);
	}

	efault($_POST['uris']['template'], "View");
	efault($_POST['uris']['status'], 4);
	efault($_POST['uris']['prefix'], "app/views/");
?>

<?php
	$sb->import("util/form");
	$form = new form("model:uris  action:$action  url:$submit_to");
	echo $form->open('class="pages_form"');
?>
	<div class="field">
		<?php
			echo $form->text("title");
			echo $form->tag("label  id:link-label  content:Permalink:");
			echo $form->tag("span  id:permalink  class:link-span  content:".uri("")."<span class=\"editable\">".(($_POST['uris']['path']) ? $_POST['uris']['path'] : ".." )."</span>");
			if (isset($sb->errors['uris']['path']['exists'])) echo "<span class=\"clear error\">".$sb->errors['uris']['path']['exists']."</span><br />";
		?>
		<?php if ($action == "update") { ?>
			<div class="infield" style="float:left;clear:left;width:250px">
				<h3>Tags</h3>
				<input type="text" id="tagbox" class="text left" style="width:195px" /><a class="round right button" href="javascript:apply_tags();">apply</a>
				<ul id="applied_tags">
					<?php foreach($sb->query("uris,tags", "select:DISTINCT tag, raw_tag  where:uris.id='".$_POST['uris']['id']."'", true) as $tag) { ?>
						<li><a href="javascript:remove_tag('<?php echo $tag['tag']; ?>');">x</a> <?php echo $tag['tag']; ?></li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
		<div class="infield">
			<?php
				echo $form->select("template", $templates);
				$options_file = $_POST['uris']['prefix']."templates/options/".$_POST['uris']['template'].".php";
				if (($action == "update") && (file_exists($options_file))) include($options_file);
				echo $form->select("status", $this->statuses);
				echo $form->select("collective", $collectives);
				echo $form->select("parent", $parent_ops);
				echo $form->submit("class:big round left button  value:".ucwords($action));
			?>
		</div>
		<div class="left">
			<?php echo $form->text("path  nolabel:  style:width:630px;display:none"); ?>
		</div>
	</div>
	<?php
	if (($action == "update") && (!empty($containers))) {
		$l = new form("model:new-leaf");
		$r = new form("model:remove-leaf");
		foreach($containers as $container) { ?>
			<fieldset>
				<legend><?php echo $container; ?></legend>
				<?php
					echo $sb->get("uris")->fields($container, $_POST['uris']['path']);
					echo $l->select($container."  nolabel:true  class:left", $leaf_types);
					$leaves = $sb->query("leafs", "where:page='".$_POST['uris']['path']."' && container='$container' ORDER BY position ASC");
					$rm = array("--Remove a Leaf--" => "");
					foreach($leaves as $one) $rm[$one['position']." ".$one['leaf']] = $one['position']." ".$one['leaf'];
					echo $r->select($container."  nolabel:true  class:left", $rm);
					echo $form->submit("class:round right button  value:Update");
				?>
			</fieldset>
		<?php } ?>
	<?php } ?>
</form>
