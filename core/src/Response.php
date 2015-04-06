<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Response.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * Response class
 * @ingroup core
 */
class Response {

	public $headers;
	public $cookies;
	public $code;
	public $type = "text/html";
	public $charset = "UTF-8";

	public $codes = array(
		200 => 'OK',
		301 => 'Moved Permanently',
		302 => 'Found',
		304 => 'Not Modified',
		400 => 'Bad Request',
		403 => 'Forbidden',
		404 => 'Not Found',
		408 => 'Request Timeout',
		429 => 'Too Many Requests',
		500 => 'Internal Server Error',
		503 => 'Service Unavailable'
	);

	public $theme;
	public $template = "html";
	public $layout;

	protected $output;

	public function __construct(TemplateInterface $output, $status_code = 200, $headers = array()) {
		$this->output = $output;
		$this->code = $status_code;
		$this->headers = $headers;
		$this->cookies = array();
	}

	public function assign($key, $value = "") {
		$this->output->assign($key, $value);
	}

	public function send_headers() {
		$code = $this->code;
		if (isset($this->codes[$code])) $code .= " ".$this->codes[$code];
		header("HTTP/1.1 ".$code);
		header('Content-Type: '.$this->type.'; charset='.$this->charset);
	 foreach ($headers as $name => $value) {
		 header($name.": ".$value);
	 }
	}

	public function send_cookies() {
	 foreach ($this->cookies as $name => $cookie) {
		 setcookie($name, $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
	 }
	}

	public function send_content() {
		$this->output->render($this->template, array("response" => $this));
	}

	public function send() {
		ob_start();
		$this->send_headers();
		$this->send_cookies();
		$this->send_content();
		ob_end_flush();
	}
}
