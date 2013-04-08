<?php
	$first = reset($links);
?>
		<ul class="nav nav-tabs nav-stacked<?php if (!empty($first['parent'])) echo " collapse"; ?>" id="menu-<?php echo $first['parent']; ?>">
		<?php foreach ($links as $idx => $link) { ?>
			<li class="<?php if (!empty($link['children'])) echo "parent"; ?>" data-menu-id="<?php echo $link['id']; ?>">
				<div class="right">
				<?php
					$model = "menus";
					$_POST["menus"] = array("id" => $link['id']);
					open_form("model:$model  action:delete", "class:delete_form  onsubmit:return confirm('Are you sure you want to delete this item?');");
				?>
				<button class="Delete" type="submit"><div class="sprite icon"></div></button>
				<? close_form(); ?>
				</div>
				<a href="#" class="dojoDndHandle"></a>
				<a href="<?php echo empty($link['href']) ? uri($link['path']) : ((0 === strpos($link['href'], 'http')) ? $link['href'] : uri($link['href'])); ?>"><?php echo empty($link['content']) ? $link['title'] : $link['content']; ?></a>
				<?php
					if (!empty($link['children'])) {
						echo '<a class="menu-toggle" onclick="var m = document.getElementById(\'menu-'.$link['id'].'\'); if (m.className.indexOf(\'in\') == -1) m.className = \'menu_items collapse in\'; else m.className = \'menu_items collapse\';" href="javascript:;"><i class="icon-chevron-right"></i></a>';
						assign("links", $link['children']);
						render("sortable-menu");
					} else echo '<br class="clear"/>';
				?>
			</li>
		<?php } ?>
		</ul>
