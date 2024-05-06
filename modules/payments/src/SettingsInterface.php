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
   * @param string $gateway The gateway name.
   * @param string $key the name of the settings entry, such as 'site_name' or 'theme'.
   * @param boolean $reset Pass true to reset cache.
   */
  public function get($gateway, $key, $reset = false);
}
