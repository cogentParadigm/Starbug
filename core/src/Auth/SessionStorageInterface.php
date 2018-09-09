<?php
namespace Starbug\Core;

interface SessionStorageInterface {
  /**
   * obtain the users active session claim
   * simply retrieves the token provided by the request
   */
  function load();
  function set($key, $value, $secure = false);
  function get($key);
  /**
   * store the session
   */
  function save();
  /**
   * destroy the active session
   */
  function destroy();
}
