<?php declare(strict_types = 1);

namespace Modette\Translation\Utils;

final class LocaleHelper
{

	public static function checkValid(string $locale): void
	{
		if (preg_match('/^[a-z0-9@_\\.\\-]*$/i', $locale) !== 1) {
			throw new InvalidArgumentException(sprintf('Invalid "%s" locale.', $locale));
		}

		$normalizedLocale = self::normalize($locale);
		if ($normalizedLocale !== $locale) {
			throw new InvalidArgumentException(sprintf('Invalid "%s" locale, use "%s" format instead.', $locale, $normalizedLocale));
		}
	}

	public static function normalize(string $locale): string
	{
		//TODO - implement
		// en_US, en_us, en-us =====> en-US
		// EN, En              =====> en
		// něco je snad v nette request
		return $locale;
	}

	public static function shorten(string $locale): string
	{
		return substr($locale, 0, 2);
	}

	/**
	 * @param string[] $whitelist
	 */
	public static function isWhitelisted(string $locale, array $whitelist): bool
	{
		$normalizedLocale = self::normalize($locale);

		if (array_key_exists($normalizedLocale, $whitelist)) {
			return true;
		}

		if (array_key_exists(self::shorten($normalizedLocale), $whitelist)) {
			return true;
		}

		return false;
	}

}
