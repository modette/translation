<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Locale\LocaleConfigurator;
use Nette\Http\IResponse;

final class CookieLocaleConfigurator implements LocaleConfigurator
{

	/** @var IResponse */
	private $response;

	public function __construct(IResponse $response)
	{
		$this->response = $response;
	}

	public function configure(string $locale): void
	{
		$this->response->setCookie(CookieLocaleResolver::COOKIE_KEY, $locale, '1 year');
	}

}
