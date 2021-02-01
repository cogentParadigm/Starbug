<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Starbug\Bundle\BundleFactoryInterface;
use Starbug\Bundle\BundleInterface;

class FormHandlerMiddleware implements MiddlewareInterface {

  protected $models;
  protected $db;
  protected $filter;
  protected $response;
  protected $bundles;
  protected $logger;

  /**
   * Dependencies for handling form submissions.
   *
   * @param ModelFactoryInterface $models Factory to create models.
   * @param DatabaseInterface $db Database.
   * @param InputFilterInterface $filter Utility for input sanitization.
   * @param ResponseFactoryInterface $response Response factory.
   * @param BundleFactoryInterface $bundles Factory to create bundles.
   * @param LoggerInterface $logger Logger.
   */
  public function __construct(
    ModelFactoryInterface $models,
    DatabaseInterface $db,
    InputFilterInterface $filter,
    ResponseFactoryInterface $response,
    BundleFactoryInterface $bundles,
    LoggerInterface $logger
  ) {
    $this->models = $models;
    $this->db = $db;
    $this->filter = $filter;
    $this->responseFactory = $response;
    $this->bundles = $bundles;
    $this->logger = $logger;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $permitted = $this->checkPost($request->getParsedBody());
    if (false === $permitted) {
      return $this->response->createResponse(302)
        ->withHeader("Location", "login?to=".$request->getUri()->getPath());
    } elseif (true === $permitted) {
      $request = $request->withAttribute("state", $this->getErrorState($request));
    }
    return $handler->handle($request);
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
    return null;
  }

  protected function getErrorState(ServerRequestInterface $request): BundleInterface {
    $model = array_keys($request->getParsedBody()["action"])[0];
    $errors = $this->models->get($model)->errors("", true);
    $state = $this->bundles->create($errors);
    return $state;
  }
}
