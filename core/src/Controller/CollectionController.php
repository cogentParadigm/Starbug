<?php
namespace Starbug\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use Starbug\Core\ApiController;

class CollectionController extends ApiController {
  public function __invoke($collection, $model = false, $format = "json"): ResponseInterface {
    if (false !== $model) {
      $this->api->setModel($model);
    }
    $this->api->setFormat($format);
    return $this->api->render($collection);
  }
}
