<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Logger.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * logger
 */
class Logger implements LoggerInterface {
	protected $db;
	protected $request;
	public function __construct(DatabaseInterface $db, Request $request) {
		$this->db;
		$this->request = $request;
	}
	/**
	 * @copydoc LoggerInterface::emergency
	 */
	public function emergency($message, array $context = array()) {
		$this->log("emergency", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::alert
	 */
	public function alert($message, array $context = array()) {
		$this->log("alert", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::critical
	 */
	public function critical($message, array $context = array()) {
		$this->log("critical", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::error
	 */
	public function error($message, array $context = array()) {
		$this->log("error", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::warning
	 */
	public function warning($message, array $context = array()) {
		$this->log("warning", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::notice
	 */
	public function notice($message, array $context = array()) {
		$this->log("notice", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::info
	 */
	public function info($message, array $context = array()) {
		$this->log("info", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::debug
	 */
	public function debug($message, array $context = array()) {
		$this->log("debug", $message, $context);
	}
	/**
	 * @copydoc LoggerInterface::log
	 */
	public function log($level, $message, array $context = array()) {
		$message = $this->interpolate($message, $context);
		$location = $url = "http" . (($this->request->server['HTTPS'] == "on") ? "s://" : "://") . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		$referrer = $this->request->server['HTTP_REFERRER'];
		$this->db->store("logs", array("severity" => $level, "message" => $message, "location" => $location, "referrer" => $referrer));
	}
	/**
	* Interpolates context values into the message placeholders.
	*/
	function interpolate($message, array $context = array()) {
		// build a replacement array with braces around the context keys
		$replace = array();
		foreach ($context as $key => $val) {
			$replace['{' . $key . '}'] = $val;
		}
		// interpolate replacement values into the message and return
		return strtr($message, $replace);
	}
}
