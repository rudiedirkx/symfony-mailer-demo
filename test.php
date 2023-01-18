<?php

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

$transport = Transport::fromDsn(sprintf('smtp://%s:%s@%s', urlencode(MAILBOX_USER), urlencode(MAILBOX_PASS), MAILBOX_HOST));
$mailer = new Mailer($transport);
// dd($transport, $mailer);

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
