<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Exception\MalformedLocaleException;
use Modette\Translation\Locale\LocaleHelper;
use Modette\Translation\Locale\LocaleResolver;
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

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		$locale = $this->request->getCookie(self::COOKIE_KEY);

		if ($locale === null) {
			return null;
		}

		if (!is_string($locale)) {
			$this->response->deleteCookie(self::COOKIE_KEY);
			return null;
		}

		try {
			LocaleHelper::validate($locale);
		} catch (MalformedLocaleException $error) {
			$this->response->deleteCookie(self::COOKIE_KEY);
			return null;
		}

		return $locale;
	}

}
