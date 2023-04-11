<?php

use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailApiTransport extends AbstractTransport {
	public function __construct(
		protected Gmail $gmail,
		EventDispatcherInterface $dispatcher = null,
		LoggerInterface $logger = null,
	) {
		parent::__construct($dispatcher, $logger);
	}
	public function __toString() : string {
		return 'gmail+api';
	}
	protected function doSend(SentMessage $sfMessage) : void {
		$raw = $sfMessage->getMessage()->toString();

		$gmailMessage = new Message();
		$gmailMessage->setRaw(base64_encode($raw));
// dd($gmailMessage);

		$this->gmail->users_messages->send('me', $gmailMessage);
	}
}
