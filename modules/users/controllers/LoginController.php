<?php
namespace Starbug\Users;

use GuzzleHttp\Psr7\Uri;
use Starbug\Core\Controller;
use Starbug\Core\SessionHandlerInterface;
use Starbug\Core\IdentityInterface;
use Starbug\Core\Routing\RouterInterface;

class LoginController extends Controller {
  public function __construct(SessionHandlerInterface $session, IdentityInterface $user, RouterInterface $router) {
    $this->session = $session;
    $this->user = $user;
    $this->router = $router;
  }
  public function defaultAction() {
    if ($this->user->loggedIn()) {
      $redirectPath = "";
      if ($this->user->loggedIn('admin') || $this->user->loggedIn('root')) $redirectPath = "admin";
      $queryParams = $this->request->getQueryParams();
      if (!empty($queryParams["to"])) {
        $redirectPath = $this->filterRedirectPath($queryParams["to"], $redirectPath);
      }
      $this->response->redirect($redirectPath);
    } else {
      $this->render("login.html");
    }
  }
  public function logout() {
    $this->session->destroy();
    $this->response->redirect("");
  }
  public function forgotPassword() {
    $this->render("forgot-password.html");
  }
  public function resetPassword() {
    $this->render("reset-password.html");
  }
  protected function filterRedirectPath($input, $default = "") {
    $uri = new Uri($input);
    if (!Uri::isAbsolutePathReference($uri) || Uri::isRelativePathReference($uri)) {
      return $default;
    }
    $request = $this->request->withUri($uri);
    $route = $this->router->route($request);
    if ($route["action"] == "missing") {
      return $default;
    }
    return (string) $uri;
  }
}
