<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/emails/src/Mailer.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class Mailer implements MailerInterface {

	private $host;
	private $username;
	private $password;
	private $from_email;
	private $from_name;
	private $port;
	private $secure;
	private $macro;

	function __construct(SettingsInterface $settings, MacroInterface $macro) {
		$this->settings = $settings;
		$this->macro = $macro;
		$this->host = $settings->get("email_host");
		$this->username = $settings->get("email_username");
		$this->password = $settings->get("email_password");
		$this->from_email = $settings->get("email_address");
		$this->from_name = $settings->get("site_name");
		$this->port = $settings->get("email_port");
		$this->secure = $settings->get("email_secure");
	}

	function create() {
		$mailer = new PHPMailer(true);
		if ($this->host) {
			$mailer->IsSMTP(); // send via SMTP
			$mailer->Host     = $this->host;
			$mailer->SMTPAuth = true;  // turn on SMTP authentication
			$mailer->Username = $this->username;    // SMTP username
			$mailer->Password = $this->password;    // SMTP password
		}
		if ($this->from_email) $mailer->From = $this->from_email;
		if ($this->from_name) $mailer->FromName = $this->from_name;
		if (!empty($this->port)) $mailer->Port = $this->port;
		if (!empty($this->secure)) $mailer->SMTPSecure = $this->secure;
		$mailer->WordWrap = 50;
		$mailer->IsHTML(true);
		return $mailer;
	}

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send($options = array(), $data = array()) {
		$mailer = $this->create();

		$options = $options;
		$data = $data;
		$data['url_flags'] = 'u';

		//get template params
		if (!empty($options['template'])) {
			$template = query("email_templates")->condition(array(
				"name" => $options['template'],
				"email_templates.statuses" => "published"
			))->one();
			if (!empty($template)) $options = array_merge($template, $options);
		}

		//set mailer params
		if (!empty($options['from'])) $mailer->From = $this->macro->replace($options['from'], $data);
		if (!empty($options['from_name'])) $mailer->FromName = $this->macro->replace($options['from_name'], $data);
		if (!empty($options['subject'])) $mailer->Subject = $this->macro->replace($options['subject'], $data);
		if (!empty($options['body'])) $mailer->Body = $this->macro->replace($options['body'], $data);
		if (!empty($options['to'])) {
			$to = $options['to'];
			if (!is_array($to)) $to = explode(",", $to);
			foreach ($to as $email) $mailer->AddAddress($this->macro->replace(trim($email), $data));
		}
		if (!empty($options['cc'])) {
			if (!is_array($options['cc'])) $options['cc'] = explode(',', $options['cc']);
			foreach ($options['cc'] as $cc) $mailer->AddCC($this->macro->replace($cc, $data));
		}
		if (!empty($options['bcc'])) {
			if (!is_array($options['bcc'])) $options['bcc'] = explode(',', $options['bcc']);
			foreach ($options['bcc'] as $bcc) $mailer->AddBCC($this->macro->replace($bcc, $data));
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
}
