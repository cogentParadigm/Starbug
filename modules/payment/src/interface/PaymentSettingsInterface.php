<?php
namespace Starbug\Payment;
/**
 * a simple interface for retrieving settings
 */
interface PaymentSettingsInterface {

	public function testMode($gateway);
	/**
	 * get a settings value
	 * @param string $name the name of the settings entry, such as 'site_name' or 'theme'
	 */
	public function get($gateway, $key, $reset = false);
}
