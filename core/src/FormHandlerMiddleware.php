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
   * Dependencies for handling form submissions.
   *
   * @param ModelFactoryInterface $models Factory to create models.
   * @param InputFilterInterface $filter Utility for input sanitization.
   * @param ResponseBuilderInterface $response Response builder.
   * @param LoggerInterface $logger Logger.
   */
  public function __construct(
    ModelFactoryInterface $models,
    InputFilterInterface $filter,
    ResponseBuilderInterface $response,
    LoggerInterface $logger
  ) {
    $this->models = $models;
    $this->filter = $filter;
    $this->response = $response;
    $this->logger = $logger;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $permitted = $this->checkPost($request->getParsedBody());
    if ($permitted) {
      return $handler->handle($request);
    } else {
      return $this->response->redirect("login?to=".$request->getUri()->getPath())->getResponse();
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
  protected function checkPost($post) {
    if (!empty($post['action']) && is_array($post['action'])) {
      foreach ($post['action'] as $key => $val) {
        return $this->postAction($this->filter->normalize($key), $this->filter->normalize($val), $post[$key]);
      }
    }
    return true;
  }
}
