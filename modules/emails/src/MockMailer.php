<?php
namespace Starbug\Emails;

class MockMailer implements MailerInterface {
  protected array $messages = [];

  public function send($options = [], $data = [], $rendered = false) {
    $this->messages[] = [
      'options' => $options,
      'data' => $data,
      'rendered' => $rendered
    ];
  }

  public function getMessages(): array {
    return $this->messages;
  }

  public function getLastMessage(): ?array {
    return $this->messages[count($this->messages) - 1] ?? null;
  }

  public function clear(): void {
    $this->messages = [];
  }
}
