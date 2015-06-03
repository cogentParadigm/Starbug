<?php
class MailTask implements TaskInterface {
	public function __construct(MailerInterface $mailer) {
		$this->mailer = $mailer;
	}
	public function process($item, $queue) {
		try {
			$this->mailer->send($item['data']);
			$queue->success($item);
		} catch (Exception $e) {
			$queue->error($item, $e->getMessage());
		}
	}
}
?>
