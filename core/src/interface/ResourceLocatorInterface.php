<?php
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
  function className($class, $suffix = false);
}
