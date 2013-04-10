<?php
$sb->provide("util/mailer");
include(BASE_DIR."/util/phpmailer/class.phpmailer.php");
class mailer extends PHPMailer {
	private $host;
	private $username;
	private $password;
	private $from_email;
	private $from_name;

	# TODO: add getters/setters for private vars

	function __construct() {
		$this->host = settings("email_host");
		$this->username = settings("email_username");
		$this->password = settings("email_password");
		$this->from_email = settings("contact_email_address");
		$this->from_name = settings("site_name");
		if ($this->host) {
			$this->IsSMTP(); // send via SMTP
			$this->Host     = $this->host;
			$this->SMTPAuth = true;  // turn on SMTP authentication
			$this->Username = $this->username;    // SMTP username
			$this->Password = $this->password;    // SMTP password
		}
		if ($this->from_email) $this->From = $this->from_email;
		if ($this->from_name) $this->FromName = $this->from_name;
		$this->WordWrap = 50;
		$this->IsHTML(true);
	}

	function quickSend($data) {
		$this->Subject = $data['subject'];
		$this->Body = $data['body'];
		if(!isset($data['email'])) {
			$data['email'] = Etc::DEFAULT_EMAIL_ADDRESS;
			#$data['email'] = "tony@neonrain.com";
		}
		$this->AddAddress($data['email']);
		$result = $this->Send();
		$this->ClearAddresses();
		return $result;
	}

	function getErrors() {
		return $this->ErrorInfo;
	}

}
?>
