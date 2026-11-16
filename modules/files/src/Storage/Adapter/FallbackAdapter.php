<?php
namespace Starbug\Files\Storage\Adapter;

use ElGigi\FlysystemUsefulAdapters\FallbackAdapter as ParentAdapter;
use Starbug\Files\Storage\AdapterInterface;
use Starbug\Http\UriBuilderInterface;
use Throwable;

/**
 * Reads from the first adapter holding the file, so a local filesystem can fall
 * back to a remote one, such as a stage server serving published files.
 */
class FallbackAdapter extends ParentAdapter implements AdapterInterface {
  protected array $adapters = [];
  protected ?UriBuilderInterface $uri = null;
  public function __construct(AdapterInterface $adapter, AdapterInterface ...$fallback) {
    parent::__construct($adapter, ...$fallback);
    $this->adapters = [$adapter, ...$fallback];
  }
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    foreach ($this->adapters as $adapter) {
      try {
        if ($adapter->fileExists($path)) {
          return $adapter->getUrl($path, $absolute);
        }
      } catch (Throwable $e) {
        continue;
      }
    }
    return $this->adapters[0]->getUrl($path, $absolute);
  }
}
