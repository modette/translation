<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Locale\LocaleConfigurator;
use Nette\Http\Session;

final class SessionLocaleConfigurator implements LocaleConfigurator
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
