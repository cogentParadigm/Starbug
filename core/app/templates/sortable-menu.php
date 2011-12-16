		<ol class="menu_items" data-dojo-type="dojo.dnd.Source" data-dojo-props="withHandles: true">
		<?php foreach ($links as $idx => $link) { ?>
			<?php $children = query("uris_menus,uris", "select:uris_menus.*,uris.title,uris.path  where:uris_menus.parent=?  orderby:position ASC", array($link['id'])); ?>
			<li class="dojoDndItem<?php if (!empty($children)) echo " parent"; ?>" data-menu-id="<?php echo $link['id']; ?>">
				<div class="right">
				<?php
					assign("model", "uris_menus");
					$_POST["uris_menus"] = array("id" => $link['id']);
					render_form("delete");
				?>
				</div>
				<a href="#" class="dojoDndHandle"></a>
				<a href="<?php echo uri($link['path']); ?>"><?php echo $link['title']; ?></a>
				<?php
					if (!empty($children)) {
						assign("links", $children);
						render("sortable-menu");
					} else echo '<br class="clear"/>';
				?>
			</li>
		<?php } ?>
		</ol>
