<?php
if (($_SESSION[P('memberships')] & 1) != 1) {
	$submit_to = uri("sb-admin");
	include("core/app/views/login.php");
} else { ?>
<h2 class="title">Bridge</h2>
<div class="bridge">
<?php include("core/app/views/sb/uris/default.php"); ?>
</div>
<?php } ?>
