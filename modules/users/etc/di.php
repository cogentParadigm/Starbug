<?php
namespace Starbug\Users;

use DI;

return [
  "routes" => DI\add([
    "login" => [
      "title" => "Login",
      "controller" => "login"
    ],
    "logout" => [
      "controller" => "login",
      "action" => "logout"
    ],
    "forgot-password" => [
      "title" => "Forgot Password",
      "controller" => "login",
      "action" => "forgotPassword"
    ],
    "reset-password" => [
      "title" => "Reset Password",
      "controller" => "login",
      "action" => "resetPassword"
    ]
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Users\Migration")
  ])
];
