<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Operation\Operation;
use Starbug\Queue\QueueManagerInterface;
use Starbug\Users\ForgotPassword\Worker;

class ForgotPassword extends Operation {
  protected $queues;
  public function __construct(QueueManagerInterface $queues) {
    $this->queues = $queues;
  }
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $fields = $data->get();
    $email = trim($fields['email']);
    $this->queues->put(Worker::class, ["email" => $email]);
    return $state;
  }
}
