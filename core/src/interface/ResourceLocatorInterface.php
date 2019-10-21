<?php
namespace Starbug\Core;

interface ResourceLocatorInterface {
  /**
   * Locate a resource by name and scope/type.
   *
   * @param string $name the the name of the resource
   * @param string $scope the type or scope of resource, such as 'templates' or 'views'
   *
   * @return array file paths
   */
  public function locate($name, $scope = "templates");
  public function className($class, $suffix = false);
}
