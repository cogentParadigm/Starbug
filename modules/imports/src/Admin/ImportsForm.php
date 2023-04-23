<?php
namespace Starbug\Imports\Admin;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use League\Flysystem\MountManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Core\Pager;
use Starbug\Core\TemplateInterface;
use Starbug\Imports\OperationsRepository;

class ImportsForm extends FormDisplay {
  protected $source_keys = [];
  protected $source_values = [];
  public $model = "imports";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    DatabaseInterface $db,
    MountManager $filesystems,
    OperationsRepository $operations
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->operations = $operations;
  }
  public function buildDisplay($options) {
    if ($options['operation'] == "run") {
      $this->buildRun($options);
    } else {
      $this->buildDefault($options);
    }
  }
  protected function buildDefault($options) {
    $this->add("name");
    $this->add(["model", "input_type" => "hidden", "default" => $options['model'] ?? ""]);
    $this->add([
      "operation",
      "input_type" => "select",
      "default" => "import"
    ] + $this->getOperationOptions($this->get("model") ?? $options["model"] ?? false));
    $this->add(["source", "input_type" => "text", "data-dojo-type" => "sb/form/FileList", "data-dojo-props" => "browseEnabled: true"]);
    $this->add(["worksheet", "type" => "text"]);

    $source = $this->get("source");
    $model = $this->get("model");
    $worksheet = $this->get("worksheet");
    if (!empty($source) && !empty($model)) {
      $this->add(["fields",
        "input_type" => "text",
        "data-dojo-type" => "sb/form/CRUDList",
        "data-dojo-props" => "model: 'imports_fields', dialogParams: {".
          "title: 'Add Field', urlParams: {model:'{$model}',  source:'{$source}', worksheet:'{$worksheet}'}".
        "}"
      ]);
      $this->add(["transformers",
        "input_type" => "text",
        "data-dojo-type" => "sb/form/CRUDList",
        "data-dojo-props" => "model: 'imports_transformers', dialogParams: {".
          "title: 'Add Transformer', urlParams: {model:'{$model}',  source:'{$source}', worksheet:'{$worksheet}'}".
        "}"
      ]);
    }
  }
  protected function buildRun($options) {
    $this->actions->remove($this->defaultAction);
    $source = $this->get("source");
    $output = $this->preparePaginatedOutput($source);
    if ($this->success("run")) {
      $this->add(["success", "input_type" => "html", "value" => '<p class="alert alert-success">Import completed</p>']);
    }
    $this->add(["table", "input_type" => "template", "value" => "csv-table.html", "class" => "table table-striped"] + $output);
    $this->add(["count", "input_type" => "html", "value" => "<p>".$output["pager"]->count." rows. Press import to begin.</p>"]);
    $this->actions->add(["run", "label" => "Import", "class" => "btn-success"]);
  }
  protected function getOperationOptions($model = false) {
    $operations = $this->operations->getAvailableOperations($model);
    return [
      "options" => array_column($operations, "name"),
      "values" => array_keys($operations)
    ];
  }
  protected function preparePaginatedOutput($id) {
    $worksheet = $this->get("worksheet");
    $rows = [];
    $file = $this->db->query("files")->condition("id", $id)->one();
    if ($this->filesystems->has("tmp://".$file["id"]."_".$file["filename"])) {
      $this->filesystems->delete("tmp://".$file["id"]."_".$file["filename"]);
    }
    $this->filesystems->copy(
      $file["location"]."://".$file["id"]."_".$file["filename"],
      "tmp://".$file["id"]."_".$file["filename"]
    );
    $path = $this->filesystems
      ->getFilesystem("tmp")
      ->getAdapter()
      ->applyPathPrefix($file["id"]."_".$file["filename"]);
    $type = IOFactory::identify($path);
    $reader = IOFactory::createReader($type);
    if (!empty($worksheet)) {
      $reader->setLoadSheetsOnly($worksheet);
    }
    $spreadsheet = $reader->load($path);
    $count = $spreadsheet->getActiveSheet()->getHighestDataRow() - 1;
    $maxCol = $spreadsheet->getActiveSheet()->getHighestDataColumn();
    $size = 20;
    $queryParams = $this->request->getQueryParams();
    $pager = new Pager($count, $size, intval($queryParams["pg"] ?? 1));
    $from = $pager->start + 1;
    $to = $pager->finish + 1;
    $rows = $spreadsheet->getActiveSheet()->rangeToArray("A" . $from . ":" . $maxCol . $to);
    unset($queryParams["pg"]);
    $prefix = $this->request->getUri()->getPath()."?";
    if (!empty($queryParams)) {
      $prefix .= http_build_query($queryParams).'&';
    }
    $prefix .= "pg=";
    $half = floor($pager->range/2);
    // set $from to $current_page minus half of $range OR 1
    $fromPage = ($pager->current_page > $half) ? $pager->current_page - $half : 1;
    // set $to to the full range from from
    $toPage = $fromPage + $pager->range;
    // if that pushes us past the end, shift back to the end
    if ($toPage > $pager->last) {
      $toPage = $pager->last;
      $fromPage = $toPage - $pager->range;
    }
    // if there are not enough pages, bring up $from to 1
    if ($fromPage < 1) {
      $fromPage = 1;
    }
    return ["rows" => $rows, "pager" => $pager, "url" => $prefix, "fromPage" => $fromPage, "toPage" => $toPage];
  }
}
