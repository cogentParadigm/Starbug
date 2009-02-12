<?php $id = next($this->uri);
	if (!empty($this->errors['uris'])) include("core/app/nouns/uris/".(($id)?"update":"create").".php");
	else {
		if (!$id) $uri = $this->get("uris")->find("*", "", "LIMIT 1")->fields();
		else $uri = $this->get("uris")->find("*", "id='".$id."'")->fields();
?>
<h2><?php echo $uri['path']; ?></h2>
<dl>
	<dt>Template</dt><dd><?php echo $uri['template']; ?></dd>
	<dt>Visible</dt><dd><?php echo $uri['visible']; ?></dd>
	<dt>Importance</dt><dd><?php echo $uri['importance']; ?></dd>
	<dt>Security</dt><dd><?php echo $uri['security']; ?></dd>
	<dt>Options</dt>
	<dd>
		<a class="button" href="<?php echo uri("uris/update/$uri[id]"); ?>" style="float:left">Edit</a>
		<form id="del_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
			<input name="action[uris]" type="hidden" value="delete"/>
			<input type="hidden" name="uris[id]" value="<?php echo $uri['id']; ?>"/>
			<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/>
		</form>
	</dd>
</dl>
<?php } ?>