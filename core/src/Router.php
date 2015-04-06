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
		$route = array("controller" => "main", "action" => "missing", "arguments" => array());

		$paths = $this->expand($request->path);
		$query = $this->db->query("uris")->condition("path", $paths);
		$query->sort("FIELD('".implode("', '", $paths)."')");

		foreach ($query as $result) {
			$permitted = query("uris")->condition("id", $result['id'])->action("read")->one();
			if ($permitted) {
				$route = $result;
				break;
			} else {
				$route = array("controller" => "main", "action" => "forbidden", "arguments" => array());
			}
		}

		if (empty($route['controller']) && !empty($route['type'])) {
			$route = array_replace(array('controller' => $route['type'], 'action' => 'show'), $route);
			$route['controller'] = $route['type'];
			if (empty($route['action'])) $route['action'] = 'show';
		}

		return $route;
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
