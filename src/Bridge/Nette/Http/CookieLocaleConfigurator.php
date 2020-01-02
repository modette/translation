<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use DateTimeInterface;
use Modette\Translation\Locale\LocaleConfigurator;
use Nette\Http\IResponse;

final class CookieLocaleConfigurator implements LocaleConfigurator
{

	/** @var IResponse */
	private $response;

	/** @var string|int|DateTimeInterface */
	private $expiration = '1 year';

	public function __construct(IResponse $response)
	{
		$this->response = $response;
	}

	/**
	 * @param string|int|DateTimeInterface $expiration
	 */
	public function setCookieExpiration($expiration): void
	{
		$this->expiration = $expiration;
	}

	public function configure(string $locale): void
	{
		$this->response->setCookie(CookieLocaleResolver::COOKIE_KEY, $locale, $this->expiration);
	}

}
