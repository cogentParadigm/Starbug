<?php
namespace Starbug\Wizard\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Routing\Controller;

class WizardController extends Controller {
  protected $model;
  protected $pageTemplate;
  protected $formTemplate;
  public function __construct(DatabaseInterface $db, ServerRequestInterface $request) {
    $this->db = $db;
    $this->request = $request;
  }
  public function __invoke() {
    return $this->render($this->pageTemplate, $this->getViewParams());
  }
  public function form() {
    return $this->render($this->formTemplate, $this->getViewParams());
  }

  protected function getViewParams($arguments = []) {
    $arguments += $this->request->getAttribute("route")->getOptions();
    $arguments["formParams"] = $this->getDisplayOptions($arguments["formParams"] ?? []);
    return $arguments;
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
    $queryParams = $this->request->getQueryParams();
    $bodyParams = $this->request->getParsedBody();
    if (!empty($bodyParams["back"])) {
      $options["step"] = $bodyParams["back"];
    } elseif (!empty($bodyParams["step"])) {
      $options["step"] = $bodyParams["step"];
      if ($this->db->errors()) {
        $options["step"]--;
      }
    } elseif (!empty($queryParams["step"])) {
      $options["step"] = $queryParams["step"];
    }

    // If we're past creating the user record, set the correct ID.
    if (!empty($bodyParams["id"])) {
      $options["id"] = $bodyParams["id"];
    } elseif (!is_null($this->db->getInsertId($this->model))) {
      $options["id"] = $this->db->getInsertId($this->model);
    }

    return $options;
  }
}
