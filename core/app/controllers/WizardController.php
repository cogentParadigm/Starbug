<?php

namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WizardController extends Controller {
  protected $model;
  protected $pageTemplate;
  protected $formTemplate;
  public function __construct(DatabaseInterface $db, ServerRequestInterface $request) {
    $this->db = $db;
    $this->request = $request;
  }
  public function defaultAction() {
    return $this->render($this->pageTemplate, ["options" => $this->getDisplayOptions()]);
  }
  public function form() {
    return $this->render($this->formTemplate, ["options" => $this->getDisplayOptions()]);
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
    if (empty($options["step"])) {
      $options["step"] = 1;
    }

    // Set the correct step.
    $queryParams = $this->request->getQueryParams();
    $bodyParams = $this->request->getParsedBody();
    if (!empty($queryParams["step"])) {
      $options = $queryParams;
    } elseif (!empty($bodyParams[$this->model]["step"])) {
      $options["step"] = $bodyParams[$this->model]["step"];
      if ($this->db->errors()) {
        $options["step"]--;
      }
    }

    // If we're past creating the user record, set the correct ID.
    if (!empty($bodyParams[$this->model]["id"])) {
      $options["id"] = $bodyParams[$this->model]["id"];
    } elseif (!is_null($this->db->getInsertId($this->model))) {
      $options["id"] = $this->db->getInsertId($this->model);
    }

    return $options;
  }
}
