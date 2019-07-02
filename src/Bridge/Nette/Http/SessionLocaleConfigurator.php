<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Nette\Http\Session;

final class SessionLocaleConfigurator
{

	/** @var Session */
	private $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	public function configure(string $locale): void
	{
		$this->session->getSection(SessionLocaleResolver::SECTION)->offsetSet(SessionLocaleResolver::PARAMETER, $locale);
	}

}
