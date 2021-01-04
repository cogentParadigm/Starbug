<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Starbug\Http\ResponseBuilderInterface;

class FormHandlerMiddleware implements MiddlewareInterface {

  protected $models;
  protected $session;
  protected $filter;
  protected $response;
  protected $logger;

  /**
   * All the dependencies needed to co-ordinate the application.
   *
   * @param ModelFactoryInterface $models Factory to create models.
   * @param SessionHandlerInterface $session Session for authenticated users.
   * @param InputFilterInterface $filter Utility for input sanitization.
   * @param ResponseBuilderInterface $response Response builder.
   * @param LoggerInterface $logger Logger.
   */
  public function __construct(
    ModelFactoryInterface $models,
    SessionHandlerInterface $session,
    InputFilterInterface $filter,
    ResponseBuilderInterface $response,
    LoggerInterface $logger
  ) {
    $this->models = $models;
    $this->session = $session;
    $this->filter = $filter;
    $this->response = $response;
    $this->logger = $logger;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $permitted = $this->checkPost($request->getParsedBody(), $request->getCookieParams());
    if ($permitted) {
      return $handler->handle($request);
    } else {
      return $this->response->redirect("login?to=".$request->getUri()->getPath());
    }
  }
  /**
   * Run a model action if permitted.
   *
   * @param string $key The model name.
   * @param string $value The function name.
   * @param array $post The posted data.
   *
   * @return mixed result of function call or nothing.
   */
  protected function postAction($key, $value, $post = []) {
    $this->logger->addInfo("Attempting action ".$key.' -> '.$value);
    if ($object = $this->models->get($key)) {
      return $object->post($value, $post);
    }
  }

  /**
   * Check $_POST['action'] for posted actions and run them through postAction.
   */
  protected function checkPost($post, $cookies) {
    if (!empty($post['action']) && is_array($post['action'])) {
      // Validate csrf token for authenticated requests.
      if ($this->session->loggedIn()) {
        $validated = false;
        if (!empty($cookies['oid']) && !empty($post['oid']) && $cookies['oid'] === $post['oid']) $validated = true;
        if (true !== $validated) {
          return false;
        }
      }
      // Execute post actions.
      foreach ($post['action'] as $key => $val) return $this->postAction($this->filter->normalize($key), $this->filter->normalize($val), $post[$key]);
    }
    return true;
  }
}
