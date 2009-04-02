			<ul id="footer">
				<li><a href="http://www.starbugphp.com">StarbugPHP WSDK</a> &copy; 2008-2009 <a href="http://www.aligangji.com">Ali Gangji</a></li>
				<li><a href="http://www.starbugphp.com/freedoms">freedoms</a></li>
			</ul>
		</div>
		<?php if (($_SESSION[P('memberships')] & 1)==1) { ?>
		<div id="dash">
			<script type="text/javascript">
				function add_uri() {
					dojo.xhrGet({
						url: '<?php echo uri("uris/add"); ?>',
						load: function (data) {
							dojo.byId('dash_form').innerHTML += data;
						}
					});
				}
				function save_add() {
					var loader = dojo.byId('loading');
					dojo.style(loader, {visibility:"visible"});
					dojo.xhrPost({
						url: '<?php echo uri("uris/add"); ?>',
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
				<li class="first"><a href="<?php echo uri("models"); ?>">model</a></li>
				<li><a href="<?php echo uri("generate"); ?>">generate</a></li>
				<!--<li><a class="add" href="uris/create" onclick="add_uri();return false;">+</a><a href="<?php echo uri("uris"); ?>">uris</a></li>
				<li><a class="add" href="users/create" onclick="add_user();return false;">+</a><a href="<?php echo uri("users"); ?>">users</a></li>
				<li id="loading" style="visibility:hidden"></li>-->
			</ul>
		</div>
		<?php } ?>
	</body>
</html>
