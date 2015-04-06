<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/RouterInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
class Router implements RouterInterface {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	/**
	 * a router must identify a controller from a Request
	 * @param Request $request the request object
	 * @return array the controller information using the following keys:
	 *										- controller: the controller name
	 *										- action: the action name
	 *										- arguments: the arguments
	 */
	public function route(Request $request) {
		$paths = $this->expand($request->path);
		$query = $this->db->query("uris")->condition("path", $paths);
		$query->sort("FIELD('".implode("', '", $paths)."')");
	}
	protected function expand($path) {
		$expanded = array();
		$parts = explode("/", $path);
		foreach ($parts as $idx => $part) {
			if ($idx) $part = $expanded[0].'/'.$part;
			array_unshift($expanded, $part);
		}
		return $expanded;
	}
}
