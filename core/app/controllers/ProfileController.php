<?php
namespace Starbug\Core;

class ProfileController extends Controller {
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function init() {
    $this->assign("model", "users");
  }
  public function defaultAction() {
    $this->assign("id", $this->user->userinfo("id"));
    $this->render("profile.html");
  }
}
