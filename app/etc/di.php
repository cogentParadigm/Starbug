<?php
return [
  "routes" => DI\decorate(function ($routes) {
    $routes["home"] = [
      "title" => "Home",
      "type" => "views",
      "layout" => "home"
    ];
    $routes["login"] = [
      "title" => "Login",
      "controller" => "login"
    ];
    $routes["logout"] = [
      "controller" => "login",
      "action" => "logout"
    ];
    $routes["forgot-password"] = [
      "title" => "Forgot Password",
      "controller" => "login",
      "action" => "forgot_password"
    ];
    return $routes;
  }),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\App\Migration')
  ])
];
