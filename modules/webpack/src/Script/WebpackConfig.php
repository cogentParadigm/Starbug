<?php
namespace Starbug\Webpack\Script;

use Psr\EventDispatcher\EventDispatcherInterface;
use Starbug\Webpack\Event\ConfigEvent;
use Starbug\Webpack\Service\WebpackConfiguration;

class WebpackConfig {
  protected WebpackConfiguration $webpack;
  protected string $base_directory;
  public function __construct(WebpackConfiguration $webpack, EventDispatcherInterface $eventDispatcher, $base_directory) {
    $this->webpack = $webpack;
    $this->eventDispatcher = $eventDispatcher;
    $this->base_directory = $base_directory;
  }
  public function __invoke() {
    if (!file_exists($this->base_directory."/var/etc")) {
      passthru("mkdir ".$this->base_directory."/var/etc");
    }
    $config = $this->webpack->getWebpackConfig();
    file_put_contents($this->base_directory."/var/etc/webpack.config.js", $config);
    $this->eventDispatcher->dispatch(new ConfigEvent());
  }
}
