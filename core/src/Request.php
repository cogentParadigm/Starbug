<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/Request.php
* @author Ali Gangji <ali@neonrain.com>
* @ingroup core
*/
namespace Starbug\Core;
/**
* Request class. interprets the request (URI, $_GET, and $_POST) and serves the appropriate content
* @ingroup core
*/
class Request implements RequestInterface {
	protected $url;
	protected $language = "en";
	protected $post = array();
	protected $headers = array();
	protected $files = array();
	protected $cookies = array();

	public function __construct(URLInterface $url) {
		$this->setURL($url);
	}
	public function setURL(URLInterface $url) {
		$this->url = $url;
		$parts = explode(".", $url->getHost());
		if (count($parts) > 2 && strlen($parts[0]) == 2) $this->setLanguage($parts[0]);
		return $this;
	}
	public function getURL() {
		return $this->url;
	}
	public function setPath($path) {
		$this->url->setPath($path);
		return $this;
	}
	public function getPath() {
		return $this->url->getPath();
	}
	public function setParameter($name, $value) {
		$this->url->setParameter($name, $value);
		return $this;
	}
	public function hasParameter($name) {
		return $this->url->hasParameter($name);
	}
	public function getParameter($name) {
		return $this->url->getParameter($name);
	}
	public function setParameters($parameters) {
		$this->url->setParameters($parameters);
		return $this;
	}
	public function getParameters() {
		return $this->url->getParameters();
	}
	public function getFormat() {
		return $this->url->getFormat();
	}
	public function getComponent($index=0) {
		return $this->url->getComponent($index);
	}
	public function getComponents() {
		return $this->url->getComponents();
	}
	public function setLanguage($language) {
		$this->language = $language;
		return $this;
	}
	public function getLanguage() {
		return $this->language;
	}
	public function setPost($post) {
		$args = func_get_args();
		$target = &$this->post;
		$value = array_pop($args);
		foreach ($args as $arg) {
			$target = &$target[$arg];
		}
		$target = $value;
		return $this;
	}
	public function getPost() {
		$args = func_get_args();
		$value = $this->post;
		foreach ($args as $arg) {
			$value = $value[$arg];
		}
		return $value;
	}
	public function hasPost($post) {
		$args = func_get_args();
		$value = $this->post;
		$arg = array_shift($args);
		while (!empty($args)) {
			$arg = array_shift($args);
			if (isset($value[$arg])) {
				$value = $value[$arg];
			} else {
				return false;
			}
		}
		return true;
	}
	public function setHeader($header, $value) {
		$this->headers[$header] = $value;
		return $this;
	}
	public function setHeaders($headers) {
		$this->headers = $headers;
		return $this;
	}
	public function getHeaders() {
		return $this->headers;
	}
	public function getHeader($name) {
		return $this->headers[$name];
	}
	public function setFiles($files) {
		$this->files = $files;
		return $this;
	}
	public function getFiles() {
		return $this->files;
	}
	public function setCookie($name, $value) {
		$this->cookies[$name] = $value;
		return $this;
	}
	public function getCookie($name) {
		return $this->cookies[$name];
	}
	public function setCookies($cookies) {
		$this->cookies = $cookies;
		return $this;
	}
	public function getCookies() {
		return $this->cookies;
	}
}
