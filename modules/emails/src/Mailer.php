<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/emails/src/Mailer.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
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

	function render($options = array(), $data = array()) {
		$data['url_flags'] = 'u';
		//get template params
		if (!empty($options['template'])) {
			$template = query("email_templates")->condition("name", $options['template'])->one();
			if (!empty($template)) $options = array_merge($template, $options);
		}
		//set mailer params
		$arr = array("to", "cc", "bcc");
		$replace = array("from", "from_name", "subject", "body", "to", "cc", "bcc");
		foreach ($replace as $key) {
			if (!empty($options[$key])) {
				if (in_array($key, $arr) && !is_array($options[$key])) {
					$options[$key] = explode(",", $options[$key]);
				}
				if (is_array($options[$key])) {
					foreach ($options[$key] as $idx => $value) $options[$key][$idx] = $this->macro->replace(trim($value), $data);
				} else {
					$options[$key] = $this->macro->replace($options[$key], $data);
				}
			}
		}
		return $options;
	}

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send($options = array(), $data = array(), $rendered=false) {
		$mailer = $this->create();
		if (!$rendered) $options = $this->render($options, $data);
		//set mailer params
		if (!empty($options['from'])) $mailer->From = $options['from'];
		if (!empty($options['from_name'])) $mailer->FromName = $options['from_name'];
		if (!empty($options['subject'])) $mailer->Subject = $options['subject'];
		if (!empty($options['body'])) $mailer->Body = $options['body'];
		if (!empty($options['to'])) {
			foreach ($options['to'] as $email) $mailer->AddAddress($email);
		}
		if (!empty($options['cc'])) {
			foreach ($options['cc'] as $cc) $mailer->AddCC($cc);
		}
		if (!empty($options['bcc'])) {
			foreach ($options['bcc'] as $bcc) $mailer->AddBCC($bcc);
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
