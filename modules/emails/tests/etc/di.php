<?php

use function DI\autowire;

return [
  'Starbug\Core\MailerInterface' => autowire('Starbug\Core\MockMailer')
];
