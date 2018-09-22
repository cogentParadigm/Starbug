<?php
namespace Starbug\Core;

class MockMailer implements MailerInterface {
  public function send($options = [], $data = [], $rendered = false) {
    // Do nothing.
  }
}
