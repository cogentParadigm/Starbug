<?php
namespace Starbug\Core;

/**
 * Cookie based implementation of SessionStorageInterface
 */
class SessionStorage implements SessionStorageInterface {

  private $request;
  private $key;
  private $data = [];
  private $secure = [];

  public function __construct(RequestInterface $request, $key) {
    $this->request = $request;
    $this->key = $key;
  }
  /**
   * {@inheritdoc}
   *
   * @return array The session data.
   */
  public function load() {
    // Obtain and parse session cookie.
    $session = $this->request->getCookie("sid");
    if (empty($session)) return false;
    parse_str($session, $params);
    $digest = $params['d'];
    unset($params['d']);
    // Validate cookie integrity.
    if (hash_hmac("sha256", http_build_query($params), $this->key) != $digest) return false;
    $this->data = $params;
    return $this->data;
  }
  /**
   * Set a value.
   *
   * @param string $key A property name under which to save the value.
   * @param mixed $value The value to save.
   * @param boolean $secure True if the value should be saved securely.
   *
   * @return void
   */
  public function set($key, $value, $secure = false) {
    $this->data[$key] = $value;
    $this->secure[$key] = $secure;
  }
  /**
   * Retrieve data.
   *
   * @param string $key The key/property to retrieve.
   *
   * @return mixed The value of the specified key.
   */
  public function get($key) {
    return isset($this->data[$key]) ? $this->data[$key] : null;
  }
  /**
   * {@inheritdoc}
   *
   * @return void
   */
  public function save() {
    // Encode session data.
    $session = http_build_query($this->data);
    // Append digest.
    $session .= '&d='.urlencode(hash_hmac("sha256", $session, $this->key));
    // Write cookies.
    $url = $this->request->getURL();
    $oid = md5(uniqid(mt_rand(), true));
    setcookie("sid", $session, $this->data['e'], $url->build(""), null, false, true);
    setcookie("oid", $oid, $this->data['e'], $url->build(""), null, false, false);
    $this->request->setCookie("sid", $session);
    $this->request->SetCookie("oid", $oid);
  }
  /**
   * {@inheritdoc}
   *
   * @return void
   */
  public function destroy() {
    $this->data = [];
    $url = $this->request->getURL();
    setcookie("sid", null, time(), $url->build(""), null, false, true);
  }
}
