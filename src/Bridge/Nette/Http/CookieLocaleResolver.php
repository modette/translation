<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Utils\LocaleHelper;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

final class CookieLocaleResolver implements LocaleResolver
{

	public const COOKIE_KEY = 'locale';

	/** @var IRequest */
	private $request;

	/** @var IResponse */
	private $response;

	public function __construct(IRequest $request, IResponse $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	public function resolve(): ?string
	{
		$locale = $this->request->getCookie(self::COOKIE_KEY);

		// Remove invalid cookie
		try {
			$locale !== null && LocaleHelper::checkValid($locale);
		} catch (\Throwable $error) { //TODO - specific exception
			$this->response->deleteCookie(self::COOKIE_KEY);
			return null;
		}

		return $locale ? (string) $locale : null;
	}

}
