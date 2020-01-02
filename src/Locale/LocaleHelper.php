<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

use Modette\Translation\Exception\MalformedLocaleException;
use Nette\Utils\Strings;

final class LocaleHelper
{

	/**
	 * @throws MalformedLocaleException
	 */
	public static function validate(string $locale): void
	{
		if (preg_match('/^[a-z0-9@_\\.\\-]*$/i', $locale) !== 1) {
			throw MalformedLocaleException::forUnknownFormat($locale);
		}

		$normalizedLocale = self::normalize($locale);
		if ($normalizedLocale !== $locale) {
			throw MalformedLocaleException::forNonNormalizedFormat($locale, $normalizedLocale);
		}
	}

	public static function normalize(string $locale): string
	{
		$localeParts = preg_split('#([_\-])#', $locale, -1, PREG_SPLIT_NO_EMPTY);
		$count = count($localeParts);

		if ($count === 2) {
			return sprintf('%s-%s', Strings::lower($localeParts[0]), Strings::upper($localeParts[1]));
		}

		if ($count === 1) {
			return Strings::lower($localeParts[0]);
		}

		return $locale;
	}

	public static function shorten(string $locale): string
	{
		return preg_split('#([_\-])#', $locale, -1, PREG_SPLIT_NO_EMPTY)[0];
	}

	/**
	 * @param string   $locale Normalized locale
	 * @param string[] $whitelist List of normalized locales
	 */
	public static function isWhitelisted(string $locale, array $whitelist): bool
	{
		if (in_array($locale, $whitelist, true)) {
			return true;
		}

		return in_array(self::shorten($locale), $whitelist, true);
	}

}
