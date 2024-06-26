<?php
namespace Starbug\Users\Controller;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;
use Starbug\Routing\RouterInterface;

class LoginController extends Controller {
  public function __construct(
    protected SessionHandlerInterface $session,
    protected RouterInterface $router,
    protected ServerRequestInterface $request
  ) {
  }
  public function defaultAction() {
    if ($this->session->loggedIn()) {
      $redirectPath = "";
      if ($this->session->loggedIn('admin') || $this->session->loggedIn('root')) {
        $redirectPath = "admin";
      }
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
  protected function filterRedirectPath($input, $default = "") {
    $uri = new Uri($input);
    if (!Uri::isAbsolutePathReference($uri) || Uri::isRelativePathReference($uri)) {
      return $default;
    }
    $request = $this->request->withUri($uri);
    $route = $this->router->route($request);
    if (in_array($route->getPath(), ["/missing", "/forbidden"])) {
      return $default;
    }
    return (string) $uri;
  }
}
