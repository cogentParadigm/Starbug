<?php
namespace Starbug\Core;

interface InputFilterInterface {
  /**
   * normalize a string
   * @param string $raw the raw string
   * @param string $valid_chars valid characters. default is 'a-zA-Z0-9'
   * @return string the normalized version of $raw
   */
  function normalize($raw, $valid_chars = 'a-zA-Z0-9 \-_');
  function boolean($boolean);
  function int($int);
  function float($int);
  function string($string);
  function url($url);
  function email($email);
  function plain($content);
  function html($content, $allowed = array());
  function attributes($attributes);
}
