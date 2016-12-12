<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/ResponseInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
namespace Starbug\Core;
/**
 * ResponseInterface class
 * @ingroup core
 */
interface ResponseInterface {
	public function getHeaders();
	public function setHeaders($headers = array());
	public function getHeader($name);
	public function setHeader($name, $value = null);
	public function getCookies();
	public function setCookies($cookies = array());
	public function getCookie($name);
	public function setCookie($name, $value = null);
	public function getCode();
	public function setCode($code);
	public function getContentType();
	public function setContentType($type);
	public function getCharset();
	public function setCharset($charset);
	public function getTheme();
	public function setTheme($theme);
	public function getTemplate();
	public function setTemplate($template);
	public function getLayout();
	public function setLayout($layout);
	public function getStyles();
	public function setStyles($styles = array());
	public function getStyle($name);
	public function setStyle($name, $value = null);
	public function getScripts();
	public function setScripts($scripts = array());
	public function getScript($name);
	public function setScript($name, $value = null);
	public function assign($key, $value = "");
	public function send();
	public function missing();
	public function forbidden();
	function redirect($url);
	public function capture($template, $params=array(), $options = array());
	public function js($mid);
}
