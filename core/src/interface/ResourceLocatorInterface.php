<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ResourceLocatorInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface ResourceLocatorInterface {

	/**
	 * locate a resource by name and scope/type
	 * @param string $name the the name of the resource
	 * @param string $scope the type or scope of resource, such as 'templates' or 'views'
	 * @return array file paths
	 * @TODO allow boolean return
	 */
	function locate($name, $scope = "templates");
	/**
	 * locate a resource by name and scope/type
	 * @param string $name the the name of the resource
	 * @param string $scope the type or scope of resource, such as 'templates' or 'views'
	 * @return array namespaces
	 * @TODO allow boolean return
	 */
	function locate_namespaces($name, $scope = "models");
}
