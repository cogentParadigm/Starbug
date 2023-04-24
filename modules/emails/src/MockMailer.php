<?php
namespace Starbug\Emails;

class MockMailer implements MailerInterface {
  public function send($options = [], $data = [], $rendered = false) {
    // Do nothing.
  }
}
