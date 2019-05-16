<?php
namespace Starbug\App;

use Starbug\Core\FormDisplay;

class FormWizard extends FormDisplay {
  /**
   * Entry point to build up the form.
   *
   * @param array $options Input parameters.
   *
   * @return void
   */
  public function buildDisplay($options) {
    $this->actions->remove($this->defaultAction);
    $this->attributes["class"][] = "animated";
    $this->request->setPost($this->model, "step", "");
    $this->request->setParameter("step", "");

    $this->add(["step", "input_type" => "hidden", "default" => $options["step"] + 1]);

    $this->buildStep($options);
  }

  public function buildStep($options) {
    call_user_func([$this, $this->steps[$options["step"]]], $options);
  }

  /**
   * Helper method to add page title.
   *
   * @param string $title The title.
   *
   * @return void
   */
  protected function addTitle($title, $attributes = []) {
    $this->add(["title", "input_type" => "html", "value" => '<h2 class="f5 mb4 b">'.$title.'</h2>'] + $attributes);
  }

  /**
   * Helper method to add a submit button.
   *
   * @param string $name The form action or just a machine name.
   * @param string $label The button label.
   * @param array $attributes Additional attributes to put on the button node.
   *
   * @return void
   */
  protected function addButton($name, $label, $attributes = []) {
    $this->actions->add([$name, "label" => $label] + $attributes);
  }

  /**
   * Add a save button.
   *
   * @param string $action The model action to submit to.
   * @param string $label The button label.
   * @param array $attributes Additional attributes to put on the button node.
   * @param string $id An identifier for the button, defaults to the value of $action.
   *
   * @return void
   */
  protected function addSaveButton($action, $label, $attributes = [], $id = false) {
    if (false === $id) {
      $id = $action;
    }
    // Set default options.
    $attributes = $attributes + [
      "data-submit" => "next",
      "value" => $action
    ];
    $this->addButton($id, $label, $attributes);
  }

  /**
   * Add a back button.
   *
   * @param integer $step The step number to go to.
   * @param string $label The button label.
   * @param array $attributes Additional attributes to put on the button node.
   * @param string $id An identifier for the button, defaults to the step number.
   *
   * @return void
   */
  protected function addBackButton($step, $label, $attributes = [], $id = false) {
    if (false === $id) {
      $id = $step;
    }
    // Set default options.
    $attributes = $attributes + [
      "data-submit" => "previous",
      "name" => $this->getName("step"),
      "value" => $step
    ];
    $this->addButton($id, $label, $attributes);
  }

  /**
   * Add a cancel button.
   *
   * @param string $url The url to direct to.
   * @param string $label The button label.
   * @param array $attributes Additional attributes to put on the button node.
   * @param string $id An identifier for the button, defaults to value of $url parameter.
   *
   * @return void
   */
  protected function addCancelButton($url, $label, $attributes = [], $id = false) {
    if (false === $id) {
      $id = $url;
    }
    $this->addButton($id, $label, $attributes + ["onclick" => "window.location='".$this->request->getUrl()->build($url)."'"]);
  }
}
