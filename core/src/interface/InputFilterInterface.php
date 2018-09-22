<?php
namespace Starbug\Core;

interface InputFilterInterface {
  /**
   * Normalize a string.
   *
   * @param string $raw the raw string
   * @param string $valid_chars valid characters. default is 'a-zA-Z0-9'
   *
   * @return string the normalized version of $raw
   */
  public function normalize($raw, $valid_chars = 'a-zA-Z0-9 \-_');
  public function boolean($boolean);
  public function int($int);
  public function float($int);
  public function string($string);
  public function url($url);
  public function email($email);
  public function plain($content);
  public function html($content, $allowed = []);
  public function attributes($attributes);
}
