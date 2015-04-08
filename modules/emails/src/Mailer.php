<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/emails/src/Mailer.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class Mailer extends PHPMailer implements MailerInterface {

	private $host;
	private $username;
	private $password;
	private $from_email;
	private $from_name;
	private $macro;

	function __construct(SettingsInterface $settings, MacroInterface $macro) {
		$this->macro = $macro;
		$this->host = $settings->get("email_host");
		$this->username = $settings->get("email_username");
		$this->password = $settings->get("email_password");
		$this->from_email = $settings->get("email_address");
		$this->from_name = $settings->get("site_name");
		$port = $settings->get("email_port");
		$secure = $settings->get("email_secure");
		if ($this->host) {
			$this->IsSMTP(); // send via SMTP
			$this->Host     = $this->host;
			$this->SMTPAuth = true;  // turn on SMTP authentication
			$this->Username = $this->username;    // SMTP username
			$this->Password = $this->password;    // SMTP password
		}
		if ($this->from_email) $this->From = $this->from_email;
		if ($this->from_name) $this->FromName = $this->from_name;
		if (!empty($port)) $this->Port = $port;
		if (!empty($secure)) $this->SMTPSecure = $secure;
		$this->WordWrap = 50;
		$this->IsHTML(true);
	}

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send_email($options = array(), $data = array()) {
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
		if (!empty($options['from'])) $this->From = $this->macro->replace($options['from'], $data);
		if (!empty($options['from_name'])) $this->FromName = $this->macro->replace($options['from_name'], $data);
		if (!empty($options['subject'])) $this->Subject = $this->macro->replace($options['subject'], $data);
		if (!empty($options['body'])) $this->Body = $this->macro->replace($options['body'], $data);
		if (!empty($options['to'])) {
			$to = $options['to'];
			if (!is_array($to)) $to = explode(",", $to);
			foreach ($to as $email) $this->AddAddress($this->macro->replace(trim($email), $data));
		}
		if (!empty($options['cc'])) {
			if (!is_array($options['cc'])) $options['cc'] = explode(',', $options['cc']);
			foreach ($options['cc'] as $cc) $this->AddCC($this->macro->replace($cc, $data));
		}
		if (!empty($options['bcc'])) {
			if (!is_array($options['bcc'])) $options['bcc'] = explode(',', $options['bcc']);
			foreach ($options['bcc'] as $bcc) $this->AddBCC($this->macro->replace($bcc, $data));
		}
		if (!empty($options['attachments'])) {
			$attachments = $options['attachments'];
			foreach ($attachment as $a) {
				if (is_array($a)) $this->AddAttachment($a[0], $a[1]);
				else $this->AddAttachment($a);
			}
		}

		//send mail
		$result = $this->Send();
		return $result;
	}

	/**
	 * get errors
	 */
	function errors() {
		return $this->ErrorInfo;
	}
}
