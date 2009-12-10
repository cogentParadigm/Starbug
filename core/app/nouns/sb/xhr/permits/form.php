<div class="permit">
<?php
	$sb->import("util/form");
	$name = next($this->uri);
	$privs = array("global" => "global", "table" => "table", "object" => "object"); $options = array("read" => "read"); $roles = array(); $rels = array();
	if ($sb->has($name)) {
		$sb->get($name);
		$methods = get_class_methods(ucwords($name));
		foreach($methods as $method) if (!(($method == "Table") || ($method == "query") || ($method == ucwords($name)) || ($method == "_call"))) $options[$method] = $method;
	}
	$info = unserialize(file_get_contents("var/schema/.info/".$name));
	foreach($sb->query($name) as $item) {
		$label = $info['label'];
		foreach($item as $field => $value) $label = str_replace("%".$field."%", $value, $label);
		$rels[$label] = $item['id'];
	}
	foreach(array("everone", "user", "group", "owner", "collective") as $role) $roles[$role] = $role;
	$f = new form($name, "action:grant	url:$submit_to");
	echo $f->open();
?>
		<div class="field"><?php echo $f->hidden("related_table	value:".P($name)); ?></div>
		<div class="field"><?php echo $f->select("priv_type", $privs); ?></div>
		<div class="field"><?php echo $f->select("action", $options); ?></div>
		<div class="field"><?php echo $f->select("role", $roles); ?></div>
		<div class="field"><?php echo $f->select("who"); ?></div>
		<div class="field"><?php echo $f->select("related_id", $rels); ?></div>
		<?php echo $f->submit("class:inline_button save_permit	value:save"); ?>
		<?php echo $f->tag("a	class:inline_button cancel_permit	content:cancel"); ?>
	</form>
</div>
