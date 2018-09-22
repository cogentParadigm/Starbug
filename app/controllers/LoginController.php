<?php
namespace Starbug\App;

use Starbug\Core\Controller;
use Starbug\Core\SessionHandlerInterface;
use Starbug\Core\IdentityInterface;

class LoginController extends Controller {
  public function __construct(SessionHandlerInterface $session, IdentityInterface $user) {
    $this->session = $session;
    $this->user = $user;
  }
  public function default_action() {
    if ($this->user->loggedIn()) {
      if ($this->user->loggedIn('admin') || $this->user->loggedIn('root')) $this->redirect('admin');
      else $this->redirect('');
    } else {
      $this->render("login.html");
    }
  }
  public function logout() {
    $this->session->destroy();
    $this->redirect("");
  }
  public function forgot_password() {
    $this->render("forgot-password.html");
  }
}
