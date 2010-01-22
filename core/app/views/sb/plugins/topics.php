<?php $tag = next($this->uri); $subs = $sb->subscriptions(); ?>
<h2><a href="<?php echo uri("sb/plugins/create"); ?>" class="right round button">Create Subscription</a>Subscriptions for <em><?php echo $tag; ?></em></h2>
<h3>Topics</h3>
<table class="clear lister">
<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="topic-col">Topic</th></tr><?php echo "</$t>"; ?><?php } ?>
<?php foreach($subs[$tag] as $topic => $priorities) { ?>
	<tr id="topic_<?php echo $topic; ?>">
		<td class="tag-col">
			<a href="#"><?php echo $topic; ?></a>
			<ul class="row-actions">
				<li class="first"><a href="<?php echo uri("sb/plugins/subs/$tag/$topic"); ?>">subscriptions</a></li>
			</ul>
		</td>
	</tr>
<?php } ?>
</table>
<a href="<?php echo uri("sb/plugins/create"); ?>" class="big left round button">Create Subscription</a>
