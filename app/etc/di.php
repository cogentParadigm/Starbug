<?php
return [
  "routes" => DI\decorate(function ($routes) {
    $routes[""] = [
      "title" => "Home",
      "controller" => "Starbug\App\Page\HomeController",
      "layout" => "home"
    ];
    return $routes;
  }),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\App\Migration')
  ])
];
