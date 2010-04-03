<div class="permit">
<?php
	$sb->import("util/form");
	$name = next($this->uri);
	$privs = array("global" => "global", "table" => "table", "object" => "object"); $options = array("read" => "read"); $roles = array(); $rels = array();
	if ($sb->has($name)) {
		$sb->get($name);
		$methods = get_class_methods(ucwords($name));
		foreach($methods as $method) if (!(($method == "Table") || ($method == "query") || ($method == ucwords($name)) || ($method == "__call"))) $options[$method] = $method;
	}
	if (file_exists("var/schema/.info/".$name)) $info = unserialize(file_get_contents("var/schema/.info/".$name));
	else {
		$info = array("label" => "%id%");
		$file = fopen("var/schema/.info/".$name, "wb");
		fwrite($file, serialize($info));
		fclose($file);
	}
	foreach($sb->query($name) as $item) {
		$label = $info['label'];
		foreach($item as $field => $value) $label = str_replace("%".$field."%", $value, $label);
		$rels[$label] = $item['id'];
	}
	foreach(array("everyone", "user", "group", "owner", "collective") as $role) $roles[$role] = $role;
	$f = new form($name, "action:grant  url:$submit_to");
	echo $f->open();
?>
		<div class="hidden field"><?php echo $f->hidden("related_table  value:".P($name)); ?></div>
		<div class="field"><?php echo $f->select("priv_type", $privs); ?></div>
		<div class="field"><?php echo $f->select("action", $options); ?></div>
		<div class="field"><?php echo $f->select("role", $roles); ?></div>
		<div class="field"><?php echo $f->select("who"); ?></div>
		<div class="field"><?php echo $f->select("related_id", $rels); ?></div>
		<br />
		<div class="clear field">
			<label for="statuses">Statuses</label>
			<?php foreach($this->statuses as $c => $v) { ?>
				<input type="checkbox" name="status[]" value="<?php echo $v; ?>" class="left checkbox" checked="checked" />
				<span style="display:block;margin-right:5px;font-weight:bold;font-size:11px" class="left caption"><?php echo $c; ?></span>
				
			<?php } ?>
		</div>
		<div class="clear right">
			<?php echo $f->submit("class:inline_button save_permit  value:save"); ?>
			<?php echo $f->tag("a  class:inline_button cancel_permit  content:cancel"); ?>
		</div>
	</form>
</div>
