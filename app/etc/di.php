<?php
return [
  "routes" => DI\decorate(function ($routes) {
    $routes["home"] = [
      "title" => "Home",
      "type" => "views",
      "layout" => "home"
    ];
    return $routes;
  }),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\App\Migration')
  ])
];
