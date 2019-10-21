<?php
namespace Starbug\Core;

class Display {

  public $template = "default";
  public $type = "default";

  public $options = [];
  public $attributes = ["class" => ["display"]]; // attributes for top level node
  protected $output;


  /**
   * Constructor. sets display name and options.
   *
   * @param string $name the display name
   * @param array $options the display options
   */
  public function __construct(TemplateInterface $output) {
    $this->output = $output;
  }

  /**
   * Build the display from a set of options.
   */
  public function build($options = []) {
    $this->options = $options;
    if (!empty($options["template"])) {
      $this->template = $options["template"];
    }
    $this->buildDisplay($options);
  }

  protected function buildDisplay($options) {
    // override this function
  }

  /**
   * Option getter/setter.
   */
  public function option($name, $value = null) {
    if (is_array($name)) {
      foreach ($name as $k => $v) $this->option($k, $v);
    } elseif (is_null($value)) {
      return $this->options[$name];
    } elseif (is_array($this->options[$name])) {
      $this->options[$name][] = $value;
    } else {
      $this->options[$name] = $value;
    }
  }

  /**
   * Attribute getter/setter.
   */
  public function attr($name, $value = null) {
    if (is_array($name)) {
      foreach ($name as $k => $v) $this->attr($k, $v);
    } elseif (is_null($value)) {
      return $this->attributes[$name];
    } elseif (is_array($this->attributes[$name])) {
      $this->attributes[$name][] = $value;
    } else {
      $this->attributes[$name] = $value;
    }
  }

  protected function beforeRender() {
    // extendable function
  }

  /**
   * Render the display with the specified items
   */
  public function render() {
    $this->beforeRender();
    $this->attributes["class"] = implode(" ", $this->attributes["class"]);
    $this->output->render("display/".$this->template, ["display" => $this] + $this->options);
  }
}
