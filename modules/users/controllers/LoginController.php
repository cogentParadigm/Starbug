<?php
namespace Starbug\Users;

use GuzzleHttp\Psr7\Uri;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Controller;
use Starbug\Core\Routing\RouterInterface;

class LoginController extends Controller {
  public function __construct(SessionHandlerInterface $session, RouterInterface $router) {
    $this->session = $session;
    $this->router = $router;
  }
  public function defaultAction() {
    if ($this->session->loggedIn()) {
      $redirectPath = "";
      if ($this->session->loggedIn('admin') || $this->session->loggedIn('root')) $redirectPath = "admin";
      $queryParams = $this->request->getQueryParams();
      if (!empty($queryParams["to"])) {
        $redirectPath = $this->filterRedirectPath($queryParams["to"], $redirectPath);
      }
      return $this->redirect($redirectPath);
    } else {
      return $this->render("login.html");
    }
  }
  public function logout() {
    $this->session->destroy();
    return $this->redirect("");
  }
  public function forgotPassword() {
    return $this->render("forgot-password.html");
  }
  public function resetPassword() {
    return $this->render("reset-password.html");
  }
  protected function filterRedirectPath($input, $default = "") {
    $uri = new Uri($input);
    if (!Uri::isAbsolutePathReference($uri) || Uri::isRelativePathReference($uri)) {
      return $default;
    }
    $request = $this->request->withUri($uri);
    $route = $this->router->route($request);
    if ($route["action"] ?? "" == "missing") {
      return $default;
    }
    return (string) $uri;
  }
}
