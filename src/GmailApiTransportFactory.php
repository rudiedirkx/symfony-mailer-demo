<?php

use Google\Client;
use Google\Service\Gmail;
use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\TransportInterface;

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
