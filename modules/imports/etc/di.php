<?php
namespace Starbug\Imports;

use DI;
use Starbug\Core\Operation\Delete;
use Starbug\Core\Operation\Save;
use Starbug\Imports\Admin\ImportsFieldOptions;
use Starbug\Imports\Script\SpreadsheetInfo;
use Starbug\Imports\Transform\Constant;
use Starbug\Imports\Transform\Factory;
use Starbug\Imports\Transform\Lookup;

return [
  "route.providers" => DI\add([
    DI\get(RouteProvider::class)
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
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Imports\Migration\ImportsMigration")
  ]),
  "scripts.spreadsheet-info" => SpreadsheetInfo::class,
  "Starbug\Imports\*Interface" => DI\autowire("Starbug\Imports\*"),
  "Starbug\Imports\Read\*Interface" => DI\autowire("Starbug\Imports\Read\*"),
  Factory::class => DI\autowire()
    ->constructorParameter("transformers", DI\get("importer.transformers"))
];
