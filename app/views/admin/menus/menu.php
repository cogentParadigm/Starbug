<?php if (success("menus", "create")) { ?>
	<div class="success">Menu <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div style="width:500px;" class="left">
	<h1>Update Menu '<?php echo $menu; ?>'</h1>
	<div class="btn-toolbar">
		<?php
			link_to("Add Menu Item <b class=\"icon-plus\"></b>", "admin/menus/create?menu=".$menu, "class:btn");
		?>
	</div>
	<?php
		assign("attributes", array("class" => "nav-tabs nav-stacked"));
		assign("sortable", true);
		assign("editable", true);
		assign("menu", $menu);
		render("menu");
	?>
</div>
<div id="menus_dialog" data-dojo-type="starbug/form/Dialog" data-dojo-id="menus_form" data-dojo-props="url:'<?php echo uri("admin/menus/", 'u'); ?>', post_data:{menu:'<?php echo $menu; ?>'}"></div>
<script type="text/javascript">
	require(["dojo/_base/window", "dojo/query", "dojo/on"], function(win, query, on) {
		var dragNode, offset;
		on(win.doc, '[draggable]:dragstart', function(evt) {
			this.style.opacity = 0.4;
			//this.style.position = 'absolute';
			dragNode = this;
			evt.dataTransfer.effectAllowed = 'move';
			evt.dataTransfer.setData('text', 'X');
			//var style = window.getComputedStyle(evt.currentTarget, null);
			//offset = (parseInt(style.getPropertyValue("left"),10) - evt.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - evt.clientY);
		});
		on(win.doc, '[draggable]:dragend', function(evt) {
			this.style.opacity = 1;
			//this.style.position = 'static';
		});
		on(win.doc, '[draggable]:dragenter', function(evt) {
			this.classList.add('over');
		});
		/*
		on(win.doc, 'dragover', function(evt) {
			var o = offset.split(',');
			dragNode.style.left = (evt.clientX + parseInt(o[0],10)) + 'px';
			dragNode.style.top = (evt.clientY + parseInt(o[1],10)) + 'px';
			evt.preventDefault();
			return false;
		});
		*/
		on(win.doc, '[draggable]:dragover', function(evt) {
			evt.preventDefault();
			evt.dataTransfer.dropEffect = 'move';
			return false;
		});
		on(win.doc, '[draggable]:dragleave', function(evt) {
			this.classList.remove('over');
		});
		on(win.doc, '[draggable]:drop', function(evt) {
			evt.stopPropagation();
			if (dragNode != this) {
				var list = query('> li', this.parentNode);
				list.forEach(function(node) {
					node.classList.remove('over');
				});
				var this_index = list.indexOf(this);
				var this_parent = this.getAttribute('data-parent');
				//if we're moving down within the same parent, place AFTER the drop node. In all other cases, place BEFORE the drop node
				if (this_parent == dragNode.getAttribute('data-parent') && this_index > list.indexOf(dragNode)) this.parentNode.insertBefore(dragNode, list[this_index+1])
				else this.parentNode.insertBefore(dragNode, this);
				//store it
				sb.get('menus').put({id:dragNode.getAttribute('data-menu-id'), menu:'<?php echo $menu; ?>', parent:this_parent, position:this_index}).then(function(result) {
					if (result.errors) {
						alert(result.errors[0].errors[0]);
					}
				});
			}
			return false;
		});

		//IE doesn't drag with unselectable="on" and otherwise only drags when the text is selected
		//This work around starts the dragging upon selection
		on(win.doc, '[draggable]:selectstart', function(evt) {this.dragDrop();return false;});

	});
</script>
