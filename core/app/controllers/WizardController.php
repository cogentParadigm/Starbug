<?php

namespace Starbug\Core;

class WizardController extends Controller {
  protected $model;
  protected $pageTemplate;
  protected $formTemplate;
  public function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
    $this->models = $models;
    $this->db = $db;
  }
  public function defaultAction() {
    $this->render($this->pageTemplate, ["options" => $this->getDisplayOptions()]);
  }
  public function form() {
    $this->response->setTemplate("xhr.xhr");
    $this->render($this->formTemplate, ["options" => $this->getDisplayOptions()]);
  }

  /**
   * Get suitable parameters for the form display, based on the current request.
   *
   * 'id' and 'step' are the external configuration parameters we want to prepare.
   *
   * The form will either submit with $_GET["step"] or $_POST[$this->model]["step"], indicating
   * the step it is intending to go to. If the step value is passed through $_GET, then
   * there might be other request parameters that should be passed through. If the step
   * is passed through $_POST, then we just want to pull the step value.
   *
   * Additionally, if a an ID is in the $_POST data or has just been created, we also
   * want to pass that ID through.
   *
   * @return void
   */
  protected function getDisplayOptions($options = []) {
    if (empty($options["step"])) $options["step"] = 1;

    // Set the correct step.
    if ($this->request->hasParameter("step")) {
      $options = $this->request->getParameters();
    } elseif ($this->request->hasPost($this->model, "step")) {
      $options["step"] = $this->request->getPost($this->model, "step");
      if ($this->db->errors()) {
        $options["step"]--;
      }
    }

    // If we're past creating the user record, set the correct ID.
    if ($this->request->hasPost($this->model, "id")) {
      $options["id"] = $this->request->getPost($this->model, "id");
    } elseif (!is_null($this->models->get($this->model)->insert_id)) {
      $options["id"] = $this->models->get($this->model)->insert_id;
    }

    return $options;
  }
}
