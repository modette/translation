<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Fixtures;

use Modette\Translation\Locale\LocaleHelper;
use Modette\Translation\Locale\LocaleResolver;

final class FakeLocaleResolver implements LocaleResolver
{

	/** @var string|null */
	private $locale;

	/** @var bool */
	private $wasCalled = false;

	public function __construct(?string $locale = null)
	{
		$this->locale = $locale;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		$this->wasCalled = true;

		if ($this->locale === null) {
			return null;
		}

		// Resolver which possibly get non-normalized locale should normalize it itself if whitelist check is needed
		// Non-normalized locale is returned to test normalization in DefaultTranslator
		$normalized = LocaleHelper::normalize($this->locale);

		if (!LocaleHelper::isWhitelisted($normalized, $localeWhitelist)) {
			return null;
		}

		return $this->locale;
	}

	public function wasCalled(): bool
	{
		return $this->wasCalled;
	}

}
