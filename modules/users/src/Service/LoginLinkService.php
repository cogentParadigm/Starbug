<?php
namespace Starbug\Users\Service;

use DateInterval;
use DateTimeImmutable;
use Starbug\Users\Db\Query\LoginTokensRepository;

class LoginLinkService {
  public function __construct(
    protected LoginTokensRepository $tokens
  ) {
  }
  public function createToken(int $userId, $ip = null): string {
    $token = bin2hex(random_bytes(32));
    // Hash the token for storage
    $hash = hash("sha256", $token);
    // Set expiry time (15 minutes from now)
    $expiry = (new DateTimeImmutable())->add(new DateInterval('PT15M'));
    $expires = $expiry->format('Y-m-d H:i:s');
    // Store only the hash, not the token itself
    $this->tokens->createToken($userId, $hash, $expires, $ip);
    // Return the plain token for sending to the user
    return $token;
  }
  public function validateToken(int $userId, string $token): bool {
    $activeTokens = $this->tokens->getTokens($userId);
    $tokenHash = hash("sha256", $token);
    foreach ($activeTokens as $activeToken) {
      if (hash_equals($activeToken['token_hash'], $tokenHash)) {
        $this->tokens->consumeToken($activeToken['id']);
        return true;
      }
    }
    return false;
  }
}
