<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

interface LocaleResolver
{

	/**
	 * Returns requested locale or null if none was requested
	 * Can return non-normalized locale, without whitelist check, it is done by translator
	 * Can optionally check locale format validity (e.g. for storage cleanup purposes)
	 *
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string;

}
