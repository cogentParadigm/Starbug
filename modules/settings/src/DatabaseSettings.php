<?php
namespace Starbug\Settings;

use Starbug\Db\DatabaseInterface;

/**
 * An implementation of the ConfigInterface which reads name/value pairs from a database table
 */
class DatabaseSettings implements SettingsInterface {

  protected $db;

  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }

  /**
   * Get a configuration value.
   *
   * @param string $name the name of the configuration entry, such as 'site_name'
   * @param string $scope the scope/category of the configuration item
   */
  public function get($key) {
    $item = $this->db->query("settings")->condition("name", $key)->one();
    return $item ? $item['value'] : false;
  }
}
