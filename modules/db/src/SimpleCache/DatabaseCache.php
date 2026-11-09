<?php
namespace Starbug\Db\SimpleCache;

use DateInterval;
use DateTimeImmutable;
use Psr\SimpleCache\CacheInterface;
use Starbug\Db\DatabaseInterface;

class DatabaseCache implements CacheInterface {
  /**
   * Per-request memo, keyed by cache key. A null value represents a miss.
   */
  protected array $loaded = [];

  public function __construct(
    protected DatabaseInterface $db
  ) {
  }

  public function get(string $key, mixed $default = null): mixed {
    return $this->getMultiple([$key], $default)[$key];
  }

  public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool {
    return $this->setMultiple([$key => $value], $ttl);
  }

  public function delete(string $key): bool {
    return $this->deleteMultiple([$key]);
  }

  public function clear(): bool {
    $this->loaded = [];
    $this->db->query("state")->delete();
    return true;
  }

  public function getMultiple(iterable $keys, mixed $default = null): iterable {
    $keys = is_array($keys) ? $keys : iterator_to_array($keys, false);
    $this->load(array_values(array_filter($keys, fn ($key) => !array_key_exists($key, $this->loaded))));
    $values = [];
    foreach ($keys as $key) {
      $values[$key] = $this->loaded[$key] ?? $default;
    }
    return $values;
  }

  public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool {
    $values = is_array($values) ? $values : iterator_to_array($values);
    if (!$values) {
      return true;
    }
    $expires = $this->resolveExpiry($ttl);
    $ids = [];
    foreach ($this->db->query("state")->condition("name", array_keys($values))->all() as $record) {
      $ids[$record["name"]] = $record["id"];
    }
    foreach ($values as $key => $value) {
      $record = ["name" => $key, "value" => json_encode($value), "expires" => $expires];
      if (isset($ids[$key])) {
        $record["id"] = $ids[$key];
      }
      $this->db->store("state", $record);
      $this->loaded[$key] = $value;
    }
    return true;
  }

  public function deleteMultiple(iterable $keys): bool {
    $keys = is_array($keys) ? $keys : iterator_to_array($keys, false);
    if (!$keys) {
      return true;
    }
    $this->db->query("state")->condition("name", $keys)->delete();
    foreach ($keys as $key) {
      unset($this->loaded[$key]);
    }
    return true;
  }

  public function has(string $key): bool {
    return $this->get($key) !== null;
  }

  /**
   * Load keys into the memo, treating expired records as absent.
   */
  protected function load(array $keys): void {
    if (!$keys) {
      return;
    }
    $records = $this->db->query("state")->condition("name", $keys)->all();
    foreach ($keys as $key) {
      $this->loaded[$key] = null;
    }
    foreach ($records as $record) {
      if ($this->isExpired($record)) {
        continue;
      }
      $this->loaded[$record["name"]] = json_decode($record["value"], true);
    }
  }

  protected function isExpired(array $record): bool {
    return !empty($record["expires"]) && $record["expires"] <= date("Y-m-d H:i:s");
  }

  /**
   * Convert a PSR-16 TTL to a DATETIME value. A null TTL never expires.
   */
  protected function resolveExpiry(null|int|DateInterval $ttl): ?string {
    if ($ttl === null) {
      return null;
    }
    $expires = new DateTimeImmutable();
    $expires = $ttl instanceof DateInterval ? $expires->add($ttl) : $expires->modify((int) $ttl." seconds");
    return $expires->format("Y-m-d H:i:s");
  }
}
