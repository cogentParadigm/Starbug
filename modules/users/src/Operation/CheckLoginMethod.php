<?php
namespace Starbug\Users\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Db\Operation\Save;

class CheckLoginMethod extends Save {
  public function __construct(
    protected DatabaseInterface $db,
    protected ServerRequestInterface $request
  ) {
  }
  public function handle(array $login, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    // Always continue to the link form. Routing on the account's auth method
    // would tell an anonymous caller which addresses exist and which of them
    // use a password; the link form offers the password form as an alternative.
    $route = $this->request->getAttribute("route");
    $hint = urlencode($login['email']);
    $route->setOption("successUrl", "login/link?email=" . $hint);
    return $this->getErrorState($state);
  }
}
