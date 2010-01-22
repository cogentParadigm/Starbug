<?php
if (($_SESSION[P('memberships')] & 1) != 1) {
	$submit_to = uri("sb-admin");
	include("core/app/views/login.php");
} else { ?>
<h2>Bridge</h2>
<div style="width:45%" class="left bridge">
<?php include("core/app/views/sb/uris/default.php"); ?>
</div>
<div style="width:45%" class="right bridge">
<?php include("core/app/views/sb/models/default.php"); ?>
<?php include("core/app/views/sb/plugins/default.php"); ?>
</div>
<div class="clear bridge">

</div>
<?php } ?>
