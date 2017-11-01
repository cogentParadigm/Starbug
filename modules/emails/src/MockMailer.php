<?php
namespace Starbug\Core;

class MockMailer implements MailerInterface {
  function send($options = [], $data = [], $rendered = false) {
    // Do nothing.
  }
}
