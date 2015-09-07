<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/ApplicationInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface ApplicationInterface {
	/**
	 * an application must simply handle requests by returning a response object
	 * @param Request $request the request object
	 * @return Response the response object
	 */
	public function handle(Request $request);
}
