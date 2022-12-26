<?php
namespace Starbug\Files;

use Psr\Http\Message\UploadedFileInterface;

interface FileUploaderInterface {
  public function upload($record, UploadedFileInterface $file) : ?array;
}
