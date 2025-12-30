<?php
namespace Starbug\Webpack\Helper;

use Starbug\Http\UriBuilderInterface;
use Starbug\Webpack\Service\WebpackConfiguration;

class WebpackHelper {
  public function __construct(
    protected WebpackConfiguration $target,
    protected UriBuilderInterface $uriBuilder
  ) {
  }
  public function helper() {
    return $this;
  }
  public function service() {
    return $this->target;
  }
  public function assetPath($file, $defaultPath) {
    return $this->target->getAssetPath($file, $defaultPath);
  }
  public function assetUrl($file, $defaultPath, bool $absolute = false) {
    $path = $this->assetPath($file, $defaultPath);
    return $this->uriBuilder->build($path, $absolute);
  }
}
