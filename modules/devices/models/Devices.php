<?php
namespace Starbug\Devices;

use Starbug\Db\DatabaseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class Devices extends Table {

  public function __construct(DatabaseInterface $db, SchemerInterface $schemer, ServerRequestInterface $request) {
    parent::__construct($db, $schemer);
    $this->request = $request;
  }

  public function register($device) {
    $token = $device['token'];
    $platform = $device['platform'];
    $environment = $device['environment'];
    $uid = $this->session->getUserId();

    $user_agent = $this->request->getHeader('HTTP_USER_AGENT');
    $exists = $this->query()->condition("token", $token)
                ->condition("platform", $platform)->condition("environment", $environment)->count();
    if ($exists) {
      // If the token exists, update the owner and user_agent.
      $this->store(["owner" => $uid, "user_agent" => $user_agent], ["token" => $token, "environment" => $environment, "platform" => $platform]);
    } else {
      $this->store(["owner" => $uid, "token" => $token, "platform" => $platform, "environment" => $environment, "user_agent" => $user_agent]);
    }
  }
}
