			<ul id="footer">
				<li><a target="_blank" href="http://www.starbugphp.com">StarbugPHP WSDK</a> &copy; 2008-2009 <a target="_blank" href="http://ali.spucket.com">Ali Gangji</a></li>
				<li><a target="_blank" href="http://www.starbugphp.com/freedoms">freedoms</a></li>
			</ul>
		</div>
		<?php if (($_SESSION[P('memberships')] & 1)==1) { ?>
		<div id="dash">
			<script type="text/javascript">
				function add_uri() {
					dojo.xhrGet({
						url: '<?php echo uri("sb/uris/add"); ?>',
						load: function (data) {
							dojo.byId('dash_form').innerHTML += data;
						}
					});
				}
				function save_add() {
					var loader = dojo.byId('loading');
					dojo.style(loader, {visibility:"visible"});
					dojo.xhrPost({
						url: '<?php echo uri("sb/uris/add"); ?>',
						form: 'add_uri_form',
						load: function(data) {
							cancel_add();
						}
					});
				}
				function cancel_add() {
					var newrow = dojo.byId('add_uri');
					newrow.parentNode.removeChild(newrow);
					var loader = dojo.byId('loading');
					dojo.style(loader, {visibility:"hidden"});
				}
			</script>
			<div id="dash_form"></div>
			<ul id="dashlist">
				<li class="first"><a href="<?php echo uri("sb-admin"); ?>">admin</a></li>
				<li><a href="<?php echo uri("sb/models"); ?>">tools</a></li>
				<li><a href="<?php echo uri("sb/settings/general"); ?>">settings</a></li>
				<li class="right"><a href="<?php echo uri("sb-admin/logout"); ?>">logout</a></li>
			</ul>
		</div>
		<?php } ?>
	</body>
</html>
