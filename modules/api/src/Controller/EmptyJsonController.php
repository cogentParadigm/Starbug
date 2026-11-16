<?php
namespace Starbug\Api\Controller;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Starbug\Api\ApiController;

class EmptyJsonController extends ApiController {
  public function __construct(
    protected ResponseFactoryInterface $response
  ) {
  }
  public function __invoke(): ResponseInterface {
    return $this->response->createResponse()
      ->withHeader("Content-Type", "application/json")
      ->withBody(Utils::streamFor("[]"));
  }
}
