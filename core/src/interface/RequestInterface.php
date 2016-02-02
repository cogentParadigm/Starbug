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
interface RequestInterface {
	public function setURL(URLInterface $url);
	public function getURL();
	public function setPath($path);
	public function getPath();
	public function setParameter($name, $value);
	public function hasParameter($name);
	public function getParameter($name);
	public function setParameters($parameters);
	public function getParameters();
	public function getFormat();
	public function getComponent($index=0);
	public function setLanguage($language);
	public function getLanguage();
	public function setPost($post);
	public function getPost();
	public function setHeader($header, $value);
	public function setHeaders($headers);
	public function getHeaders();
	public function getHeader($name);
	public function setFiles($files);
	public function getFiles();
	public function setCookie($name, $value);
	public function getCookie($name);
	public function setCookies($cookies);
	public function getCookies();
}
