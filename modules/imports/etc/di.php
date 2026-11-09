<?php
namespace Starbug\Imports;

use Starbug\Db\Operation\Save;
use Starbug\Db\Operation\Delete;
use function DI\add;
use function DI\get;
use function DI\autowire;
use Psr\Container\ContainerInterface;
use Starbug\Importer\Importer;
use Starbug\Imports\Admin\ImportsFieldOptions;
use Starbug\Importer\Read\StrategyFactoryInterface;
use Starbug\Imports\Read\StrategyFactory;
use Starbug\Importer\Spreadsheet\Script\SpreadsheetInfo;
use Starbug\Importer\Transform\Constant;
use Starbug\Imports\Transform\Lookup;
use Starbug\Log\LoggerFactory;

return [
  "route.providers" => add([
    get(RouteProvider::class)
  ]),
  "importer.operations" => [
    "import" => [
      "name" => "Import",
      "class" => Save::class
    ],
    "delete" => [
      "name" => "Delete",
      "class" => Delete::class
    ]
  ],
  "importer.transformers" => add([
    "lookup" => [
      "name" => "Lookup",
      "class" => Lookup::class,
      "settings" => [
        [
          "name" => "field",
          "label" => "Field",
          "input_type" => "select",
          "resolve_options" => ImportsFieldOptions::class,
          "optional" => ""
        ],
        ["name" => "by", "label" => "By Column"],
        [
          "name" => "delimiter",
          "label" => "Delimiter",
          "default" => ","
        ]
      ]
    ],
    "constant" => [
      "name" => "Constant",
      "class" => Constant::class,
      "settings" => [
        [
          "name" => "field",
          "label" => "Field",
          "input_type" => "select",
          "resolve_options" => ImportsFieldOptions::class,
          "optional" => ""
        ],
        ["name" => "value", "label" => "Value"]
      ]
    ]
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Imports\Migration\ImportsMigration")
  ]),
  "scripts.spreadsheet-info" => SpreadsheetInfo::class,
  "importer.logger" => function (ContainerInterface $c) {
    return $c->get(LoggerFactory::class)->create("imports");
  },
  Importer::class => autowire()->method("setLogger", get("importer.logger")),
  StrategyFactoryInterface::class => autowire(StrategyFactory::class)
];
