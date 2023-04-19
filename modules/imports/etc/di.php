<?php
namespace Starbug\Imports;

use function DI\add;
use function DI\get;
use function DI\autowire;
use DI;
use Starbug\Core\Operation\Delete;
use Starbug\Core\Operation\Save;
use Starbug\Imports\Admin\ImportsFieldOptions;
use Starbug\Imports\Script\SpreadsheetInfo;
use Starbug\Imports\Transform\Constant;
use Starbug\Imports\Transform\Factory;
use Starbug\Imports\Transform\Lookup;

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
  "importer.transformers" => [
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
  ],
  "db.schema.migrations" => add([
    get("Starbug\Imports\Migration\ImportsMigration")
  ]),
  "scripts.spreadsheet-info" => SpreadsheetInfo::class,
  "Starbug\Imports\*Interface" => autowire("Starbug\Imports\*"),
  "Starbug\Imports\Read\*Interface" => autowire("Starbug\Imports\Read\*"),
  "Starbug\Imports\Write\*Interface" => autowire("Starbug\Imports\Write\*"),
  Factory::class => autowire()
    ->constructorParameter("transformers", get("importer.transformers"))
];
