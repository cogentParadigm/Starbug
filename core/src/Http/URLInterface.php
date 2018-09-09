<?php
namespace Starbug\Core;

/**
 * URLBuilder class. generate absolute URLs from relative paths and modifiers
 */
interface URLInterface {
  public function setScheme($scheme);
  public function getScheme();
  public function setHost($host);
  public function getHost();
  public function setPort($port);
  public function getPort();
  public function setUser($user);
  public function getUser();
  public function setPassword($password);
  public function getPassword();
  public function setDirectory($dir);
  public function getDirectory();
  public function getComponent($index = 0);
  public function getComponents();
  public function setPath($path);
  public function getPath();
  public function setFormat($format);
  public function getFormat();
  public function setParameter($name, $value);
  public function setParameters($parameters = array());
  public function hasParameter($name);
  public function getParameter($name);
  public function getParameters();
  public function removeParameter($name);
  public function clearParameters();
  public function setFragment($fragment);
  public function getFragment();
  public function setAbsolute($absolute);
  public function build($path = false, $absolute = false);
}
