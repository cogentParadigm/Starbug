<?php
namespace Starbug\Css;

use Starbug\Http\UriBuilderInterface;
use Starbug\ResourceLocator\ResourceLocatorInterface;
use Twig\Environment;

class CssLoader {
  protected $theme;
  protected $options = false;
  public function __construct(ResourceLocatorInterface $locator, UriBuilderInterface $uri, Environment $twig, $modules) {
    $this->locator = $locator;
    $this->uri = $uri;
    $this->twig = $twig;
    $this->modules = $modules;
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
          'href="'.$this->uri->build($style["href"]).'" '.
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
    $this->theme = $name;
    $previous = $this->modules["Starbug\Theme"];
    $this->modules["Starbug\Theme"] = "app/themes/".$name;
    $this->locator->set("Starbug\Theme", "app/themes/".$name);
    $templates = $this->twig->getLoader()->getPaths();
    $layouts = $this->twig->getLoader()->getPaths("layouts");
    $views = $this->twig->getLoader()->getPaths("views");
    foreach ($templates as $idx => $path) {
      if ($path == $previous."/templates") {
        $templates[$idx] = "app/themes/".$name."/templates";
      }
    }
    foreach ($layouts as $idx => $path) {
      if ($path == $previous."/layouts") {
        $layouts[$idx] = "app/themes/".$name."/layouts";
      }
    }
    foreach ($views as $idx => $path) {
      if ($path == $previous."/views") {
        $views[$idx] = "app/themes/".$name."/views";
      }
    }
    $this->twig->getLoader()->setPaths($templates);
    $this->twig->getLoader()->setPaths(["app/themes/".$name."/templates"], "theme");
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
          $style["href"] = $this->modules[$mid] . "/" . $style["href"];
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
