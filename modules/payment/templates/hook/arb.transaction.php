<?php
	if ($_POST['x_respones_code'] != 1) {
		//notify on failed subscription
		import("mailer");
		$mailer = new mailer();
		$result = $mailer->quickSend(array(
			'subject' => "Failed ARB transaction",
			'body' => "Failed ARB transaction for subscription ".$_POST['x_subscription_id']
		));
	}
?>
