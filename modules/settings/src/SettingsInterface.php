<?php
namespace Starbug\Settings;

/**
 * A simple interface for retrieving settings
 */
interface SettingsInterface {

  /**
   * Get a settings value.
   *
   * @param string $key the name of the settings entry, such as 'site_name' or 'theme'
   */
  public function get($key);
}
