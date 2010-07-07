	<?php global $kids; global $sb; global $request; global $dojo; ?>
	<tr id="uris_<?php echo $item['id']; ?>">
		<td class="expand-col"><?php if (!empty($kids[$item['id']])) echo '&crarr;'; ?></td>
		<td class="title-col">
			<a href="<?php echo uri("sb/uris/update/$item[id]"); ?>"><?php echo $item['title']; ?></a><br />
			<small>/<?php echo $item['path']; ?></small>
			<ul class="row-actions">
				<li class="first"><a href="<?php echo uri("sb/uris/update/$item[id]"); ?>">edit</a></li>
				<li><?php $_POST['uris']['id'] = $item['id']; echo form("model:uris  action:delete", "submit  class:link  value:delete"); ?></li>
				<li><a href="<?php echo uri($item['path']); ?>">view</a></li>
			</ul>
		</td>
		<td class="status-col">
			<?php echo date("Y/m/d", strtotime($item['modified'])); ?><br />
			<?php echo (array_search($item['status'], $request->statuses)); ?><br />
			<?php echo array_search($item['collective'], array_merge(array("everybody" => 0), $request->groups)); ?>
		</td>
	</tr>
	<?php if (!empty($kids[$item['id']])) { ?>
	<tr id="parent_<?php echo $item['id']; ?>" style="display:none">
		<td colspan="3">
		<table class="lister">
		<?php
			$dojo->toggle("#uris_".$item['id']." .expand-col", "tg_".$item['id'], "parent_".$item['id'], "default:off  add:showFunc:dojo.fx.wipeIn, hideFunc:dojo.fx.wipeOut, duration:300");
			$dojo->attach("#uris_".$item['id']." .expand-col", "sb.replace", "node:evt.target  data:switch_icon(evt.target.innerHTML)");
			foreach($kids[$item['id']] as $item) include(__FILE__); 
		?>
		</table>
		</td>
	</tr>
	<?php } ?>
