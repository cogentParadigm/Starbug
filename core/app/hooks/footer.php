		<?php if (($_SESSION[P('memberships')] & 1)==1) { ?>
		<style type="text/css">
			/* dash */
			#dash{width:100%;position:fixed;bottom:0px;}
			#dashlist{list-style:none;background:#494;margin:0 25px;height:26px;clear:both;position:relative;padding:0}
			#dashlist{-moz-border-radius-topleft:8px;-moz-border-radius-topright:8px;border-radius-topleft:8px;border-radius-topright:8px;-webkit-border-top-left-radius:8px;-webkit-border-top-right-radius:8px;overflow:hidden}
			#dashlist li{float:left;padding:0}
			#dashlist li.first{padding-left:0}
			#dashlist li.right{float:right}
			#dash a{color:#fff;font-size:12px;font-weight:bold;display:block;float:left;text-decoration:none;padding:6px 10px;border-right:2px solid}
			#dash .first a{-moz-border-radius-topleft:8px;border-radius-topleft:8px;-webkit-border-top-left-radius:8px}
			#dash .last a{-moz-border-radius-topright:8px;border-radius-topright:8px;-webkit-border-top-right-radius:8px}
			#dash li.right a{border-right:none;border-left:2px solid}
			#dash a:hover{background:#5a5}
			#dash .button{height:14px;padding:3px 5px;border:none;color:#fff;background:#494;font-size:11px;font-weight:bold;text-decoration:none;cursor:pointer}
		</style>
		<div id="dash">
			<ul id="dashlist">
				<li class="first"><a href="<?php echo uri("sb/uris"); ?>">URIs</a></li>
				<li><a href="<?php echo uri("sb/models"); ?>">Permits</a></li>
				<li style="position:absolute;left:49%;border-left:2px solid #FFF"><a href="<?php echo uri("sb-admin"); ?>">Bridge</a></li>
				<li class="last right"><a href="<?php echo uri("sb-admin/logout"); ?>">logout</a></li>
				<li class="right"><a href="<?php echo uri("sb/users"); ?>">users</a></li>
				<li class="right"><a href="<?php echo uri("sb/plugins"); ?>">plugins</a></li>
			</ul>
		</div>
		<?php } ?>
