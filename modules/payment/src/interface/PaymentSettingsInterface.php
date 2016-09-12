<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ConfigInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
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
