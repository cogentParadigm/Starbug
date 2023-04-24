<?php
namespace Starbug\Emails\Tests;

use Starbug\Emails\MailerInterface;
use Starbug\Emails\MockMailer;

use function DI\autowire;

return [
  MailerInterface::class => autowire(MockMailer::class)
];
