<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/RouterInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
class Router implements RouterInterface {
	// eg. "/user/{name}[/{id:[0-9]+}]"
	const VARIABLE_REGEX = <<<'REGEX'
\{
	\s* ([a-zA-Z][a-zA-Z0-9_-]*) \s*
	(?:
		: \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
	)?
\}
REGEX;
	const DEFAULT_DISPATCH_REGEX = '[^\/]+';
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
	public function route(RequestInterface $request) {
		$route = array("controller" => "main", "action" => "missing", "arguments" => array());

		$paths = $this->expand($request->getPath());
		$query = $this->db->query("uris")->condition("path", $paths)->select("id");
		$query->sort("FIELD(path, '".implode("', '", $paths)."')");

		foreach ($query as $result) {
			$permitted = $this->db->query("uris")->condition("uris.id", $result['id'])->action("read")->one();
			if ($permitted) {
				$route = $permitted;
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

		if (!empty($route['controller']) && empty($route['action'])) {
			$path = substr($request->getPath(), strlen($route['path']) + 1);
			if (!empty($path)) {
				$parts = explode("/", $path);
				$route['action'] = $parts[0];
			}
		}

		return $route;
	}
	public function validate(Request $request, $route, $template) {
		$path = trim(str_replace($route['path'], "", $request->getPath()), '/');
		$data = $this->parse($template);
		$values = false;
		foreach ($data as $i => $routeData) {
			list($regex, $variables) = $this->build_regex($data[$i]);
			if (!preg_match($regex, $path, $matches)) {
				continue;
			}
			$values = array();
			$idx = 1;
			foreach ($variables as $name) {
				$values[$name] = $matches[$idx];
				$idx ++;
			}
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
		$routeWithoutClosingOptionals = rtrim($route, ']');
		$numOptionals = strlen($route) - strlen($routeWithoutClosingOptionals);
		// Split on [ while skipping placeholders
		$segments = preg_split('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);
		if ($numOptionals !== count($segments) - 1) {
			// If there are any ] in the middle of the route, throw a more specific error message
			if (preg_match('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
				throw new BadRouteException("Optional segments can only occur at the end of a route");
			}
			throw new BadRouteException("Number of opening '[' and closing ']' does not match");
		}
		$currentRoute = '';
		$routeDatas = [];
		foreach ($segments as $n => $segment) {
			if ($segment === '' && $n !== 0) {
				throw new BadRouteException("Empty optional part");
			}
			$currentRoute .= $segment;
			$routeDatas[] = $this->parsePlaceholders($currentRoute);
		}
		return $routeDatas;
	}
	/**
	 * Parses a route string that does not contain optional segments.
	 */
	public function parsePlaceholders($route) {
		if (!preg_match_all('~' . self::VARIABLE_REGEX . '~x', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
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
