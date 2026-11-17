<?php
namespace Starbug\Notifications\Queue;

use Exception;
use Starbug\Notifications\NotificationManagerInterface;
use Starbug\Queue\TaskInterface;
use Starbug\Queue\QueueInterface;
use Starbug\Queue\WorkerInterface;

class ProcessNotificationsWorker implements WorkerInterface {
  public function __construct(NotificationManagerInterface $notifications) {
    $this->notifications = $notifications;
  }
  public function process(TaskInterface $task, QueueInterface $queue) {
    try {
      $data = $task->getData();
      if (false === $this->notifications->process($data["id"])) {
        $queue->fail($task);
      } else {
        $queue->complete($task);
      }
    } catch (Exception $e) {
      $queue->fail($task);
      throw $e;
    }
  }
}
