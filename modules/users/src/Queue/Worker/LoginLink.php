<?php
namespace Starbug\Users\Queue\Worker;

use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Emails\MailerInterface;
use Starbug\Http\UriBuilderInterface;
use Starbug\Queue\TaskInterface;
use Starbug\Queue\QueueInterface;
use Starbug\Queue\WorkerInterface;
use Starbug\Users\Service\LoginLinkService;

class LoginLink implements WorkerInterface {
  public function __construct(
    protected IdentityRepositoryInterface $users,
    protected LoginLinkService $loginLinkService,
    protected UriBuilderInterface $uri,
    protected MailerInterface $mailer
  ) {
  }
  public function process(TaskInterface $task, QueueInterface $queue) {
    $data = $task->getData();
    $email = $data["email"];
    $ip = $data["ip"] ?? null;
    $user = $this->users->getIdentity(["email" => $email]);
    if (!empty($user)) {
      $id = $user->getId();
      $user = $user->getData();
      $token = $this->loginLinkService->createToken($id, $ip);
      $data = ["user" => $user];
      $data['user']['login-link'] = (string) $this->uri->build('login/token/'.$id.'/'.$token, true);
      $this->mailer->send(["template" => "Login Link", "to" => $user['email']], $data);
    }
    $queue->complete($task);
  }
}
