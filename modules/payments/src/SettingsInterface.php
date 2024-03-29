<?php
namespace Starbug\Payments;

/**
 * A simple interface for retrieving settings.
 */
interface SettingsInterface {

  public function testMode($gateway);
  /**
   * Get a settings value.
   *
   * @param string $name the name of the settings entry, such as 'site_name' or 'theme'
   */
  public function get($gateway, $key, $reset = false);
}
