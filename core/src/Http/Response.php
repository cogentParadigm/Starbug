<?php
namespace Starbug\Core;
/**
 * Response class
 * @ingroup core
 */
class Response implements ResponseInterface {

	protected $headers;
	protected $cookies;
	protected $code;
	protected $content_type = "text/html";
	protected $charset = "UTF-8";

	protected $codes = array(
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
	public $layout = "views";
	public $styles;
	public $scripts;

	protected $output;

	public function __construct(TemplateInterface $output, $status_code = 200, $headers = array()) {
		$this->output = $output;
		$this->code = $status_code;
		$this->headers = new Bundle($headers);
		$this->cookies = new Bundle();
		$this->styles = new Bundle();
		$this->scripts = new Bundle();
		$this->assign("response", $this);
	}

	public function getHeaders() {
		return $this->headers;
	}
	public function setHeaders($headers = array()) {
		foreach ($headers as $name => $value) {
			$this->setHeader($name, $value);
		}
		return $this;
	}
	public function getHeader($name) {
		return $this->headers->get($name);
	}
	public function setHeader($name, $value = null) {
		$this->headers->set($name, $value);
		return $this;
	}
	public function getCookies() {
		return $this->cookies;
	}
	public function setCookies($cookies = array()) {
		foreach ($cookies as $name => $value) {
			$this->setCookie($name, $value);
		}
		return $this;
	}
	public function getCookie($name) {
		return $this->cookies->get($name);
	}
	public function setCookie($name, $value = null) {
		$this->cookies->set($name, $value);
		return $this;
	}
	public function getCode() {
		return $this->code;
	}
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}
	public function getContentType() {
		return $this->content_type;
	}
	public function setContentType($type) {
		$this->content_type = $type;
		return $this;
	}
	public function getCharset() {
		return $this->charset;
	}
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}
	public function getTheme() {
		return $this->theme;
	}
	public function setTheme($theme) {
		$this->theme = $theme;
		return $this;
	}
	public function getTemplate() {
		return $this->template;
	}
	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}
	public function getLayout() {
		return $this->layout;
	}
	public function setLayout($layout) {
		$this->layout = $layout;
		return $this;
	}
	public function getStyles() {
		return $this->styles;
	}
	public function setStyles($styles = array()) {
		foreach ($styles as $name => $style) {
			$this->setStyle($name, $style);
		}
		return $this;
	}
	public function getStyle($name) {
		return $this->styles->get($name);
	}
	public function setStyle($name, $value = null) {
		$this->styles->set($name, $value);
		return $this;
	}
	public function getScripts() {
		return $this->scripts;
	}
	public function setScripts($scripts = array()) {
		foreach ($scripts as $name => $script) {
			$this->setScript($name, $script);
		}
		return $this;
	}
	public function getScript($name) {
		return $this->scripts->get($name);
	}
	public function setScript($name, $value = null) {
		$this->scripts->set($name, $value);
		return $this;
	}
	public function assign($key, $value = "") {
		$this->output->assign($key, $value);
	}
	public function sendHeaders() {
		$code = $this->code;
		if (isset($this->codes[$code])) $code .= " ".$this->codes[$code];
		header("HTTP/1.1 ".$code);
		header('Content-Type: '.$this->content_type.'; charset='.$this->charset);
		header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0');
		foreach ($this->headers as $name => $value) {
			header($name.": ".$value);
		}
	}

	public function sendCookies() {
		foreach ($this->cookies as $name => $cookie) {
			setcookie($name, $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
		}
	}

	public function sendContent() {
		$this->output->render($this->template);
	}

	public function send() {
		ob_start();
		$this->sendHeaders();
		$this->sendCookies();
		$this->sendContent();
		ob_end_flush();
	}

	/**
	 * sends a 404 and sets the payload, path, and uri
	 */
	public function missing() {
		$this->code = 404;
	}

	/**
	 * sends a 403 and sets the payload, path, and uri
	 */
	public function forbidden() {
		$this->code = 403;
	}

	function redirect($url) {
		$this->headers['location'] = $url;
		$this->content = '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';");</script>';
	}

	/**
	 * capture a rendered view and save it as the response output
	 */
	public function capture($template, $params = array(), $options = array()) {
		$options = $options + array("scope" => "views");
		$this->content = $this->output->capture($template, $params, $options);
	}

	public function js($mid) {
		$this->scripts->set($mid, $mid);
	}
}
