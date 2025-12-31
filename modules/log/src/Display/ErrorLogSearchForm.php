<?php
namespace Starbug\Log\Display;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Core\SearchForm;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Templates\TemplateInterface;

class ErrorLogSearchForm extends SearchForm {
  protected $db;
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->db = $db;
  }
  public function buildDisplay($options) {
    parent::buildDisplay($options);
    $this->add([
      "channel",
      "input_type" => "select",
      "nolabel" => true,
      "data-filter" => $this->model,
      "options" => ["", ...$this->getChannels()]
    ]);
    $this->add([
      "level",
      "input_type" => "select",
      "nolabel" => true,
      "data-filter" => $this->model,
      "options" => "Any Level,Debug,Info,Notice,Warning,Error,Critical,Alert,Emergency",
      "values" => ",100,200,250,300,400,500,550,600"
    ]);
  }
  protected function getChannels() {
    $channels = $this->db->query("error_log")->select("DISTINCT channel")->all();
    return array_column($channels, "channel");
  }
}
