<?php
namespace Starbug\Core;
interface MailerInterface {

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send($options = array(), $data = array(), $rendered=false);
}
