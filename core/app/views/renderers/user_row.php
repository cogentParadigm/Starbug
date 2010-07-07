		<tr id="user_<?php echo $item['id']; ?>">
			<td>
				<a href="<?php echo uri("sb/users/update/$item[id]"); ?>"><?php echo $item['username']; ?></a>
				<ul class="row-actions">
					<li class="first"><a href="<?php echo uri("sb/users/update/$item[id]"); ?>">edit</a></li>
					<li><?php $_POST['users'] = $item; echo form("model:users  action:delete", "submit  class:link  value:delete"); ?></li>
				</ul>
			</td>
			<td><?php global $request; $glist = ""; foreach($request->groups as $g => $n) if ($item['memberships'] & $n) $glist .= $g.", "; echo rtrim($glist, ", "); ?></td>
		</tr>
