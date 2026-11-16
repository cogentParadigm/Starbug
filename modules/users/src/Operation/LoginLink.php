<?php
namespace Starbug\Users\Operation;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Operation\Operation;
use Starbug\Queue\QueueManagerInterface;
use Starbug\Users\Queue\Worker\LoginLink as Worker;

class LoginLink extends Operation {
  protected $queues;
  protected $request;
  public function __construct(QueueManagerInterface $queues, ServerRequestInterface $request) {
    $this->queues = $queues;
    $this->request = $request;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $email = trim($data['email']);
    $ip = $this->getClientIp($this->request);
    $this->queues->put(Worker::class, ["email" => $email, "ip" => $ip]);
    $route = $this->request->getAttribute("route");
    $hint = urlencode($email);
    $route->setOption("successUrl", "login/link/sent?email=" . $hint);
    return $state;
  }
  /**
   * Retrieve client IP from PSR-7 Request
   */
  private function getClientIp(ServerRequestInterface $request): string {
    $serverParams = $request->getServerParams();
    $remoteAddr = $serverParams['REMOTE_ADDR'] ?? "";

    // If behind a proxy, add proxy IPs to this array
    // TBD: Make this configurable
    $trustedProxies = [];
    if (in_array($remoteAddr, $trustedProxies)) {
      $headersToCheck = [
        'CF-Connecting-IP',  // Cloudflare
        'True-Client-IP',    // Akamai / Cloudflare ent.
        'X-Forwarded-For',   // Standard Proxies
        'X-Real-IP'          // Nginx proxies
      ];

      foreach ($headersToCheck as $header) {
        if ($request->hasHeader($header)) {
          $value = $request->getHeaderLine($header);
          $ips = explode(',', $value);
          $ip = trim($ips[0]);
          if ($this->isValidIp($ip)) {
            return $ip;
          }
        }
      }
    }

    return $remoteAddr;
  }
  /**
   * Helper to ensure the IP string is actually an IP
   */
  private function isValidIp(string $ip): bool {
      return (bool) filter_var($ip, FILTER_VALIDATE_IP);
  }
}
