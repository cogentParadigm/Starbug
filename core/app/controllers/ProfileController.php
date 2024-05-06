<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;

class ProfileController extends Controller {
  public function __construct(
    protected SessionHandlerInterface $session
  ) {
  }
  public function init() {
    $this->assign("model", "users");
  }
  public function defaultAction() {
    $this->assign("id", $this->session->getUserId());
    $this->render("profile.html");
  }
}
