<?php
	//CHECK THAT THIS IS AN ARB TRANSACTION (AIM TRANSACTIONS CAN BE HANDLED ON THE SPOT)
	$arb = (!empty($_POST['x_subscription_id']));

	if ($arb == true) {
		//PUBLISH ARB HOOKS
		//$_POST will be populated and include x_subscription_id and x_respones_code (1=success)
		$sb->publish("arb.transaction");
	}
	//PUBLISH GLOBAL HOOK
	$sb->publish("global.transaction");
?>
