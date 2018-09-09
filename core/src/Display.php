<?php
namespace Starbug\Core;

class Display {

  public $template = "default";
  public $type = "default";

  public $options = array();
  public $attributes = array("class" => array("display")); //attributes for top level node
  protected $output;


  /**
   * constructor. sets display name and options
   * @param string $name the display name
   * @param array $options the display options
   */
  function __construct(TemplateInterface $output) {
    $this->output = $output;
  }

  /**
   * build the display from a set of options
   */
  function build($options = []) {
    $this->options = $options;
    if (!empty($options["template"])) {
      $this->template = $options["template"];
    }
    $this->build_display($options);
  }

  function build_display($options) {
    //override this function
  }

  /**
   * option getter/setter
   */
  function option($name, $value = null) {
    if (is_array($name)) {
      foreach ($name as $k => $v) $this->option($k, $v);
    } else if (is_null($value)) {
      return $this->options[$name];
    } else if (is_array($this->options[$name])) {
      $this->options[$name][] = $value;
    } else {
      $this->options[$name] = $value;
    }
  }

  /**
  * attribute getter/setter
  */
  function attr($name, $value = null) {
    if (is_array($name)) {
      foreach ($name as $k => $v) $this->attr($k, $v);
    } else if (is_null($value)) {
      return $this->attributes[$name];
    } else if (is_array($this->attributes[$name])) {
      $this->attributes[$name][] = $value;
    } else {
      $this->attributes[$name] = $value;
    }
  }

  function before_render() {
    //extendable function
  }

  /**
   * render the display with the specified items
   */
  function render() {
    $this->before_render();
    $this->attributes["class"] = implode(" ", $this->attributes["class"]);
    $this->output->render("display/".$this->template, array("display" => $this) + $this->options);
  }
}
