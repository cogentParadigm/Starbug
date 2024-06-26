<?php
namespace Starbug\Files\Controller;

use Starbug\Routing\Controller;
use Starbug\Files\FileDownloader;

class DownloadController extends Controller {
  public function __construct(
    protected FileDownloader $fileDownloader
  ) {
  }
  public function __invoke($id) {
    $this->fileDownloader->download($id);
  }
}
