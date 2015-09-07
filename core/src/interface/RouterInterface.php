<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/RouterInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface RouterInterface {
	/**
	 * a router must identify a controller from a Request
	 * @param Request $request the request object
	 * @return array the controller information using the following keys:
	 *										- controller: the controller name
	 *										- action: the action name
	 *										- arguments: the arguments
	 */
	public function route(Request $request);
}
