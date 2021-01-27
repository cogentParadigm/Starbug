<?php
namespace Starbug\Css;

use Starbug\Http\UriBuilderInterface;
use Starbug\Modules\Configuration;
use Starbug\ResourceLocator\ResourceLocatorInterface;
use Twig\Environment;

class CssLoader {
  protected $theme;
  protected $options = false;
  public function __construct(ResourceLocatorInterface $locator, Environment $twig, Configuration $modules, $theme, $baseUrl = "/") {
    $this->locator = $locator;
    $this->twig = $twig;
    $this->modules = $modules;
    $this->theme = $theme;
    $this->baseUrl = $baseUrl;
  }
  public function getConfiguration($reload = false) {
    $this->load($reload);
    return $this->options;
  }
  public function getStylesheets() {
    $this->load();
    $stylesheets = [];
    foreach ($this->options as $media => $styles) {
      foreach ($styles as $style) {
        $stylesheets[] = '<link '.
          'rel="'.$style["rel"].'" '.
          'href="'.$this->baseUrl.$style["href"].'" '.
          'type="text/css" '.
          'media="'.$media.'">';
      }
    }
    return $stylesheets;
  }
  public function getTheme() {
    return $this->theme;
  }
  public function setTheme($name) {
    $oldPath = $this->modules->get($this->theme, "path");
    $newPath = $this->modules->get($name, "path");
    $this->modules->disable($this->theme);
    $this->theme = $name;
    $this->modules->enable($this->theme);

    $enabled = $this->modules->getEnabled();
    $this->locator->setNamespaces(array_column($enabled, "namespace"));
    $this->locator->setPaths(array_column($enabled, "path"));

    $templates = $this->twig->getLoader()->getPaths();
    $layouts = $this->twig->getLoader()->getPaths("layouts");
    $views = $this->twig->getLoader()->getPaths("views");

    foreach ($templates as $idx => $path) {
      if ($path == $oldPath."/templates") {
        $templates[$idx] = $newPath."/templates";
      }
    }
    foreach ($layouts as $idx => $path) {
      if ($path == $oldPath."/layouts") {
        $layouts[$idx] = $newPath."/layouts";
      }
    }
    foreach ($views as $idx => $path) {
      if ($path == $oldPath."/views") {
        $views[$idx] = $newPath."/views";
      }
    }
    $this->twig->getLoader()->setPaths($templates);
    $this->twig->getLoader()->setPaths([$newPath."/templates"], "theme");
    $this->twig->getLoader()->setPaths($layouts, "layouts");
    $this->twig->getLoader()->setPaths($views, "views");
    $this->options = false;
  }
  protected function load($reload = false) {
    if (false === $this->options || true == $reload) {
      $this->options = $this->readConfiguration();
    }
  }
  public function readConfiguration() {
    $options = [];
    $resources = $this->locator->locate("stylesheets.json", "etc");
    $resources = array_reverse($resources);
    foreach ($resources as $mid => $resource) {
      $stylesheets = json_decode(file_get_contents($resource), true);
      foreach ($stylesheets as $media => $styles) {
        foreach ($styles as $style) {
          if (!is_array($style)) {
            $style = ["href" => $style];
          }
          if (empty($style["rel"])) $style["rel"] = "stylesheet";
          $style["href"] = $mid . "/" . $style["href"];
          $options[$media][] = $style;
        }
      }
    }
    return $options;
  }
  public function has($property, $value) {
    foreach ($this->options as $media => $styles) {
      foreach ($styles as $style) {
        if (isset($style[$property]) && $style[$property] == $value) return true;
      }
    }
    return false;
  }
}
