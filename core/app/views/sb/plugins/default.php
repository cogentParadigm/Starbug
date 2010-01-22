<h2>Plugins</h2>
<?php
	$sb->import("util/plugins");
	if (!empty($_GET['deactivate'])) {
		if ($_GET['type'] == "app") include("app/plugins/".$_GET['deactivate']."/deactivate.php");
		else if ($_GET['type'] == "core") include("core/app/plugins/".$_GET['deactivate']."/deactivate.php");
	}
	if (!empty($_GET['activate'])) {
		if ($_GET['type'] == "app") include("app/plugins/".$_GET['activate']."/activate.php");
		else if ($_GET['type'] == "core") include("core/app/plugins/".$_GET['activate']."/activate.php");
	}
	$available = plugins::available();
?>
<table id="plugins" class="clear lister">
<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="tag-col">Plugin</th></tr><?php echo "</$t>"; ?><?php } ?>
<?php foreach($available as $name => $info) { ?>
	<tr id="plugin_<?php echo $name; ?>"<?php if ($info['active']) { ?> class="active"<?php } ?>>
		<td class="plugin-col">
			<a href="#"><?php echo $name; ?></a>
			<ul class="row-actions">
				<?php if ($info['active']) { ?>
				<li class="first"><a href="<?php echo uri("sb/plugins?deactivate=".$name."&type=".$info['type']); ?>">deactivate</a></li>
				<?php } else { ?>
				<li class="first"><a href="<?php echo uri("sb/plugins?activate=".$name."&type=".$info['type']); ?>">activate</a></li>
				<?php } ?>
			</ul>
		</td>
	</tr>
<?php } ?>
</table>
