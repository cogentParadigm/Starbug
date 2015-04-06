<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/RouterInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
class Router implements RouterInterface {
	const VARIABLE_REGEX = <<<'REGEX'
	~\{
	    \s* ([a-zA-Z][a-zA-Z0-9_]*) \s*
	    (?:
	        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
	    )?
	\}~x
REGEX;
	const DEFAULT_DISPATCH_REGEX = '[^\/]+';
	public function __construct(DatabaseInterface $db=null) {
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
	public function validate(Request $request, $route, $template) {
		$data = $this->parse($template);
		list($regex, $variables) = $this->build_regex($data);
		$path = trim(str_replace($route['path'], "", $request->path), '/');
		if (!preg_match($regex, $path, $matches)) {
			return false;
		}
		$values = array();
		foreach ($variables as $idx => $name) {
			$values[$name] = $matches[$idx+1];
		}
		return $values;
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
	public function build_regex($routeData) {
		$regex = '';
		$variables = array();
		foreach ($routeData as $part) {
			if (is_string($part)) {
				$regex .= str_replace('/', '\/', preg_quote($part, '~'));
				continue;
			}
			list($varName, $regexPart) = $part;
			$variables[$varName] = $varName;
			$regex .= '(' . $regexPart . ')';
		}
		return array('/'.$regex.'/', $variables);
	}
	public function parse($route) {
		if (!preg_match_all(self::VARIABLE_REGEX, $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
			return array($route);
		}
		$offset = 0;
		$routeData = array();
		foreach ($matches as $set) {
			if ($set[0][1] > $offset) {
				$routeData[] = substr($route, $offset, $set[0][1] - $offset);
			}
			$routeData[] = array(
				$set[1][0],
				isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_DISPATCH_REGEX
			);
			$offset = $set[0][1] + strlen($set[0][0]);
		}
		if ($offset != strlen($route)) {
			$routeData[] = substr($route, $offset);
		}
		return $routeData;
	}
}
