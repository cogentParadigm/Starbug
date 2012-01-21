		<ul class="menu_items">
		<?php foreach ($terms as $idx => $term) { ?>
			<?php $children = query("terms", "where:parent=?  orderby:position ASC", array($term['id'])); ?>
			<li class="dojoDndItem<?php if (!empty($children)) echo " parent"; ?>" data-term-id="<?php echo $term['id']; ?>">
				<div class="right">
				<?php
					assign("model", "terms");
					$_POST["terms"] = array("id" => $term['id']);
					render_form("delete");
				?>
				</div>
				<input class="menu-position-field" type="text" name="<?php echo $term['id']; ?>" data-term-parent="<?php echo $term['parent']; ?>" value="<?php echo $term['position']; ?>"/>
				<span><?php echo $term['term']; ?></span>
				<?php
					if (!empty($children)) {
						assign("terms", $children);
						render("terms-list");
					} else echo '<br class="clear"/>';
				?>
			</li>
		<?php } ?>
		</ul>
