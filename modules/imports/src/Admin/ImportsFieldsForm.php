<?php
namespace Starbug\Imports\Admin;

use League\Flysystem\MountManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\CollectionFactoryInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Core\TemplateInterface;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Imports\Read\PhpSpreadsheet\IReadFilter\HeaderRowFilter;

class ImportsFieldsForm extends FormDisplay {
  protected $source_keys = [];
  protected $source_values = [];
  public $model = "imports_fields";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    DatabaseInterface $db,
    MountManager $filesystems,
    SchemerInterface $schemer
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->schema = $schemer->getSchema();
  }
  public function buildDisplay($options) {
    $data = $this->getPost();
    if ($this->success("create") && empty($data["id"])) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    $this->parseSource($options);
    $this->add(["source", "input_type" => "select", "options" => $this->source_values]);
    $dest_ops = ["destination", "input_type" => "select", "model" => $options["model"]];
    $dest_ops["resolve_options"] = ImportsFieldOptions::class;
    $this->add($dest_ops);
    $this->add(["update_key", "input_type" => "checkbox", "value" => "1", "label" => "Use this field as a key to update records"]);
  }
  protected function parseSource($options) {
    $id = $options["source"];
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
    if (!empty($options["worksheet"])) {
      $reader->setLoadSheetsOnly($options["worksheet"]);
    }
    $reader->setReadFilter(new HeaderRowFilter());
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    $this->source_keys = array_keys($data[1]);
    $this->source_values = array_values($data[1]);
  }
}
