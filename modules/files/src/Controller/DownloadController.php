<?php
namespace Starbug\Files\Controller;

use Starbug\Routing\Controller;
use Starbug\Files\FileDownloader;
use Psr\Http\Message\ResponseInterface;

class DownloadController extends Controller {
  public function __construct(
    protected FileDownloader $fileDownloader
  ) {
  }
  public function __invoke($id): ResponseInterface {
    return $this->fileDownloader->download($id);
  }
}
