<?php
namespace Starbug\Users\Db\Query;

use Starbug\Db\DatabaseInterface;

class LoginTokensRepository {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function createToken(int $userId, string $hash, string $expires, $ip = null): void {
    $this->db->store("login_tokens", [
      "users_id" => $userId,
      "token_hash" => $hash,
      "expires" => $expires,
      "ip_address" => $ip
    ]);
  }
  /**
   * Get active tokens for a user. We do not match by token to avoid timing attacks.
   * Instead, you should compare hashes with hash_equals() in your service layer.
   *
   * @param int $userId
   *
   * @return array List of active tokens.
   */
  public function getTokens(int $userId): array {
    return $this->db->query("login_tokens")
      ->condition("users_id", $userId)
      ->condition("expires", date("Y-m-d H:i:s"), ">=")
      ->condition("consumed", 0)
      ->all();
  }
  public function consumeToken(int $id): void {
    $this->db->store("login_tokens", [
      "id" => $id,
      "consumed" => 1
    ]);
  }
}
