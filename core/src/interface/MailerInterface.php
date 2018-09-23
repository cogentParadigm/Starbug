<?php
namespace Starbug\Core;

interface MailerInterface {
  /**
   * Send an email email.
   *
   * @param array $options
   * @param array $data
   */
  public function send($options = [], $data = [], $rendered = false);
}
