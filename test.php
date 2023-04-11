<?php

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

class GmailApiTransportFactory extends AbstractTransportFactory {
	protected function getSupportedSchemes() : array {
		return ['gmail+api'];
	}
	public function create(Dsn $dsn) : TransportInterface {
		if (\in_array($dsn->getScheme(), $this->getSupportedSchemes(), true)) {
			$google = new Client();
			$google->setAccessToken($dsn->getHost());
			return new GmailApiTransport(new Gmail($google));
		}

		throw new UnsupportedSchemeException($dsn, 'native', $this->getSupportedSchemes());
	}
}

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

// GMAIL API
// $dsn = sprintf('gmail+api://%s', GMAIL_ACCESS_TOKEN);
// $transport = (new GmailApiTransportFactory())->create(Dsn::fromString($dsn));
// dd($transport);
$google = new Client();
$google->setAccessToken(GMAIL_ACCESS_TOKEN);
$transport = new GmailApiTransport(new Gmail($google));
// dd($transport);

// SMTP
$dsn = sprintf('smtp://%s:%s@%s', urlencode(MAILBOX_USER), urlencode(MAILBOX_PASS), MAILBOX_HOST);
// $transport = new EsmtpTransport(MAILBOX_HOST, 465);
// $transport->setUsername(MAILBOX_USER);
// $transport->setPassword(MAILBOX_PASS);
// dd($transport);
// $transport = (new EsmtpTransportFactory())->create(Dsn::fromString($dsn));
// dd($transport);
// $transport = Transport::fromDsn($dsn);
// dd($transport);

$mailer = new Mailer($transport);
dd($mailer);

$email = (new Email())
	->from(new Address(MAILBOX_USER, "Sf User"))
	->to(SEND_TO)
	->subject('From symfony/mailer')
	->text('Through SMTP mailbox.')
	// ->html('<p>Through <b>SMTP</b> <u>mailbox</u>.</p>')
;

try {
	$mailer->send($email);
	dd("Message sent!");
}
catch (TransportException $ex) {
	dd($ex->getMessage());
}
