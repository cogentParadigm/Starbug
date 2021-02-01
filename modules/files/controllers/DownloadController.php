<?php
namespace Starbug\Files;

use Starbug\Core\Controller;

class DownloadController extends Controller {
  public function __construct(FileDownloader $fileDownloader) {
    $this->fileDownloader = $fileDownloader;
  }
  public function __invoke($id) {
    $this->fileDownloader->download($id);
  }
}
