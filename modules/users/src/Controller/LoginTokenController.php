<?php
namespace Starbug\Users\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;
use Starbug\Users\Service\LoginLinkService;

class LoginTokenController extends Controller {
  public function __construct(
    protected SessionHandlerInterface $session,
    protected IdentityRepositoryInterface $users,
    protected LoginLinkService $loginLinkService,
    protected ServerRequestInterface $request
  ) {
  }
  public function __invoke($id, $token) {
    $isGet = $this->request->getMethod() == "GET";
    if ($isGet && $this->loginLinkService->validateToken($id, $token)) {
      $user = $this->users->getIdentity($id);
      if (!empty($user)) {
        $this->session->createSession($user);
      }
    }
    return $this->redirect('login');
  }
}
