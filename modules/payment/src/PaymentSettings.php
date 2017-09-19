<?php
namespace Starbug\Payment;

use Starbug\Core\DatabaseInterface;

/**
 * An implementation of the PaymentSettingsInterface which reads name/value pairs from a database table
 */
class PaymentSettings implements PaymentSettingsInterface {

  private $db;
  private $settings;

  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
    $this->settings = array();
  }

  public function testMode($gateway) {
    $field_name = (is_numeric($gateway)) ? "id" : "name";
    $gateway = $this->db->query("payment_gateways")
      ->select("is_test_mode")
      ->condition($field_name, $gateway)->one();
    if ($gateway['is_test_mode']) return true;
    return false;
  }

  /**
  * get a configuration value
  * @param string $name the name of the configuration entry, such as 'site_name'
  * @param string $scope the scope/category of the configuration item
  */
  public function get($gateway, $setting, $reset = false) {
    $field_name = (is_numeric($gateway)) ? "id" : "name";
    $item = false;
    if (!$reset && isset($this->settings[$gateway][$setting])) {
      $item = $this->settings[$gateway][$setting];
    } else {
      $item = $this->db->query("payment_gateway_settings")
        ->select("IF(payment_gateway_id.is_test_mode=1, payment_gateway_settings.test_mode_value, payment_gateway_settings.live_mode_value) as value")
        ->condition("payment_gateway_id.$field_name", $gateway)
        ->condition("payment_gateway_settings.name", $setting)->one();
      $this->settings[$gateway][$setting] = $item;
    }
    return $item ? $item['value'] : false;
  }
}
