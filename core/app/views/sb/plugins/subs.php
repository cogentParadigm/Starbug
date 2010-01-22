<?php $tag = next($this->uri); $topic = next($this->uri); $subs = $sb->subscriptions(); ?>
<h2><a href="<?php echo uri("sb/plugins/create"); ?>" class="right round button">Create Subscription</a>Subscriptions for <em><?php echo $tag.".".$topic; ?></em></h2>
<table class="clear lister">
<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="priority-col">Priority</th><th class="handle-col">Handle</th><th class="args-col">args</th></tr><?php echo "</$t>"; ?><?php } ?>
<?php foreach($subs[$tag][$topic] as $pri => $scripts) { foreach($scripts as $i => $script) { ?>
	<tr id="<?php echo $tag.".".$topic.".".$pri.".".$i; ?>">
		<td class="priority-col"><?php echo $pri; ?></td>
		<td class="handle-col">
			<a href="#"><?php echo $script['handle']; ?></a>
			<ul class="row-actions">
				<li class="first"><a href="<?php echo uri("sb/plugins/update/$tag/$topic/$pri/$i"); ?>">edit</a></li>
				<li><a href="<?php echo uri("sb/plugins/subs/$tag/$topic?delete=$i"); ?>">delete</a></li>
			</ul>
		</td>
		<td class="args-col"><?php echo $script['args']; ?></td>
	</tr>
<?php } } ?>
</table>
<a href="<?php echo uri("sb/plugins/create"); ?>" class="big left round button">Create Subscription</a>
