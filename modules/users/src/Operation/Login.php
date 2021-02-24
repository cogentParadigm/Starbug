<?php
namespace Starbug\Users\Operation;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;

class Login extends Save {
  public function __construct(ModelFactoryInterface $models, SessionHandlerInterface $session) {
    $this->models = $models;
    $this->session = $session;
  }
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $login = $data->get();
    if ($user = $this->session->authenticate(["email" => $login['email']], $login['password'])) {
      $this->session->createSession($user);
      $this->store(["id" => $user->getId(), "last_visit" => date("Y-m-d H:i:s")]);
    } else {
      $this->error("That email and password combination was not found.", "email");
    }
    return $this->getErrorState($state);
  }
}
