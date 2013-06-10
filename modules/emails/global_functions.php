<?php
function send_email($options=array(), $data=array()) {
	$options = star($options);
	$data = star($data);
	$data['url_flags'] = 'u';
	
	//initialize mailer
	import("mailer");
	$mailer = new mailer();
	
	//get template params
	if (!empty($options['template'])) {
		$template = get("email_templates", array("name" => $options['template'], "status" => 4), array("limit" => 1));
		if (!empty($template)) $options = array_merge($template, $options);
	}
	
	//set mailer params
	if (!empty($options['from'])) $mailer->From = token_replace($options['from'], $data);
	if (!empty($options['from_name'])) $mailer->FromName = token_replace($options['from_name'], $data);
	if (!empty($options['subject'])) $mailer->Subject = token_replace($options['subject'], $data);
	if (!empty($options['body'])) $mailer->Body = token_replace($options['body'], $data);
	if (!empty($options['to'])) {
		$to = $options['to'];
		if (!is_array($to)) $to = explode(",", $to);
		foreach ($to as $email) $mailer->AddAddress(token_replace(trim($email), $data));
	}
	if (!empty($options['cc'])) {
		if (!is_array($options['cc'])) $options['cc'] = explode(',', $options['cc']);
		foreach ($options['cc'] as $cc) $mailer->AddCC(token_replace($cc, $data));
	}
	if (!empty($options['bcc'])) {
		if (!is_array($options['bcc'])) $options['bcc'] = explode(',', $options['bcc']);
		foreach ($options['bcc'] as $bcc) $mailer->AddBCC(token_replace($bcc, $data));
	}
	if (!empty($options['attachments'])) {
		$attachments = $options['attachments'];
		foreach ($attachment as $a) {
			if (is_array($a)) $mailer->AddAttachment($a[0], $a[1]);
			else $mailer->AddAttachment($a);
		}
	}
	
	//send mail
	$result = $mailer->Send();
	return $result;
}
?>
