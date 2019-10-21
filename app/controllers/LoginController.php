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
  public function defaultAction() {
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
  public function forgotPassword() {
    $this->render("forgot-password.html");
  }
}
