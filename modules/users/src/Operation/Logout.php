<?php
namespace Starbug\Users\Operation;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Operation\Operation;

class Logout extends Operation {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->session->destroy();
    return $state;
  }
}
