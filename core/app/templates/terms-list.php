		<ul class="menu_items">
		<?php foreach ($terms as $idx => $term) { ?>
			<?php $children = query("terms", "where:parent=?  orderby:position ASC", array($term['id'])); ?>
			<li class="dojoDndItem<?php if (!empty($children)) echo " parent"; ?>" data-term-id="<?php echo $term['id']; ?>">
				<div class="right" style="width:80px">
					<a class="button" href="<?php echo uri("admin/taxonomies/update/$term[taxonomy]?term=$term[id]"); ?>"><img src="<?php echo uri("core/app/public/icons/file-edit.png"); ?>"/></a>
				<?php
					assign("model", "terms");
					$_POST["terms"] = array("id" => $term['id']);
					open_form("model:terms  action:delete  url:".uri("admin/taxonomies/update/$term[taxonomy]"), "class:delete_form  onsubmit:return confirm('Are you sure you want to delete this item?');");
						button('<img src="'.uri("core/app/public/icons/cross.png").'"/>', "class:negative  title:delete");
					close_form();

				?>
				</div>
				<input class="menu-position-field" type="text" name="<?php echo $term['id']; ?>" data-term-parent="<?php echo $term['parent']; ?>" value="<?php echo $term['position']; ?>"/>
				<span><?php echo $term['term']; ?></span>
				<?php
					if (!empty($children)) {
						$this->assign("terms", $children);
						$this->render("terms-list");
					} else echo '<br class="clear"/>';
				?>
			</li>
		<?php } ?>
		</ul>
