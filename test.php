<?php

use Google\Client;
use Google\Service\Gmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

// GMAIL API
// $dsn = sprintf('gmail+api://%s', GMAIL_ACCESS_TOKEN);
// $transport = (new GmailApiTransportFactory())->create(Dsn::fromString($dsn));
// dd($transport);
// $google = new Client();
// $google->setAccessToken(GMAIL_ACCESS_TOKEN);
// $transport = new GmailApiTransport(new Gmail($google));
// dd($transport);

// SMTP
$dsn = sprintf('smtp://%s:%s@%s', urlencode(MAILBOX_USER), urlencode(MAILBOX_PASS), MAILBOX_HOST);
// $transport = new EsmtpTransport(MAILBOX_HOST, 465);
// $transport->setUsername(MAILBOX_USER);
// $transport->setPassword(MAILBOX_PASS);
// dd($transport);
// $transport = (new EsmtpTransportFactory())->create(Dsn::fromString($dsn));
// dd($transport);
$transport = Transport::fromDsn($dsn);
// dd($transport);

$mailer = new Mailer($transport);
// dd($mailer);

$email = (new Email())
	->from(new Address(MAIL_FROM ?: MAILBOX_USER, "Sf User"))
	->sender(MAILBOX_USER)
	->replyTo(sprintf('u%d@d%d.com', rand(), rand()))
	->to(MAIL_TO)
	->subject('From symfony/mailer')
	->text('Through SMTP mailbox.')
	// ->html('<p>Through <b>SMTP</b> <u>mailbox</u>.</p>')
;
dump($email->toString());

try {
	$mailer->send($email);
	dd("Message sent!");
}
catch (TransportException $ex) {
	dd($ex->getMessage());
}
