<?php

use Starbug\Core\Routing\RoutesHelper;

return [
  'routes' => DI\add(
    RoutesHelper::routes(
      [
        "title" => "Styleguide",
        "controller" => "Starbug\\Tachyons\\StyleguideController"
      ],
      [
        "styleguide" => [],
        "styleguide/colors" => ["action" => "colors"],
        "styleguide/type" => ["action" => "type"],
        "styleguide/scales" => ["action" => "scales"],
        "styleguide/content" => ["action" => "content"],
        "styleguide/controls" => ["action" => "controls"],
        "styleguide/dgrid" => ["action" => "dgrid"]
      ]
    )
  )
];
