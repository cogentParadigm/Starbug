			<ul id="footer">
				<li><a target="_blank" href="http://www.starbugphp.com">StarbugPHP WSDK</a> &copy; 2008-2009 <a target="_blank" href="http://ali.spucket.com">Ali Gangji</a></li>
			</ul>
		</div>
		<?php if (($_SESSION[P('memberships')] & 1)==1) { ?>
		<div id="dash">
			<ul id="dashlist">
				<li class="first"><a href="<?php echo uri("sb-admin"); ?>">admin</a></li>
				<li><a href="<?php echo uri("sb/pages"); ?>">pages</a></li>
				<li><a href="<?php echo uri("sb/models"); ?>">models</a></li>
				<li><a href="<?php echo uri("sb/settings/general"); ?>">settings</a></li>
				<li class="last right"><a href="<?php echo uri("sb-admin/logout"); ?>">logout</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php $sb->publish("footer"); ?>
	</body>
</html>
