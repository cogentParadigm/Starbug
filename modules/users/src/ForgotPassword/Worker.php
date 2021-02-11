<?php
namespace Starbug\Users\ForgotPassword;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\MailerInterface;
use Starbug\Http\UriBuilderInterface;
use Starbug\Queue\TaskInterface;
use Starbug\Queue\QueueInterface;
use Starbug\Queue\WorkerInterface;

class Worker implements WorkerInterface {
  public function __construct(DatabaseInterface $db, MailerInterface $mailer, UriBuilderInterface $uri) {
    $this->db = $db;
    $this->mailer = $mailer;
    $this->uri = $uri;
  }
  public function process(TaskInterface $task, QueueInterface $queue) {
    $email = $task->getData()["email"];
    $user = $this->db->query("users")->condition("email", $email)->one();
    if (!empty($user)) {
      $id = $user['id'];
      $token = bin2hex(random_bytes(16));
      $data = ["user" => $user];
      $this->db->store("users", ["id" => $id, "password_token" => $token]);
      $data['user']['password-reset-link'] = $this->uri->build('reset-password?token='.$token, true);
      $this->mailer->send(["template" => "Forgot Password", "to" => $user['email']], $data);
    }
    $queue->complete($task);
  }
}
