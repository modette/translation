<?php declare(strict_types = 1);

namespace Modette\Translation;

use MessageFormatter;
use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Resource\Catalogue;
use Modette\Translation\Utils\LocaleHelper;

final class DefaultTranslator implements ConfigurableTranslator
{

	/** @var LocaleResolver */
	private $localeResolver;

	/** @var Catalogue */
	private $catalogue;

	/** @var string */
	private $defaultLocale = 'en';

	/** @var string[] */
	private $localeWhiteList;

	/** @var string[] */
	private $fallbackLocales;

	/** @var string|null */
	private $currentLocale;

	/** @var string[]|null */
	private $currentLocaleList;

	public function __construct(LocaleResolver $localeResolver, Catalogue $catalogue)
	{
		$this->localeResolver = $localeResolver;
		$this->catalogue = $catalogue;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function translate(string $message, array $parameters = [], ?string $locale = null): string
	{
		if (($locale !== null) && !LocaleHelper::isWhitelisted($locale, $this->localeWhiteList)) {
			throw new InvalidArgumentException(sprintf('Locale "%s" is not whitelisted.', $locale));
		}

		$locale = $locale ?? $this->getCurrentLocale();
		$localeList = $this->getCurrentLocaleList();

		//TODO - měla by existovat i nějaká možnost invalidace - pro vývoj i runtime (databázové překlady)

		$translatedMessage = $this->catalogue->getMessage($message, $localeList);

		if ($translatedMessage === null) {
			//TODO - debug, logging - no translation for message found in catalogue
			return $message;
		}

		//TODO - formátování - vyžadovat ext-intl nebo mít fallback
		return MessageFormatter::formatMessage($locale, $translatedMessage, $parameters);
	}

	public function setDefaultLocale(string $defaultLocale): void
	{
		$this->defaultLocale = $defaultLocale;
	}

	/**
	 * @param string[] $localeWhiteList
	 */
	public function setLocaleWhiteList(array $localeWhiteList): void
	{
		$this->localeWhiteList = $localeWhiteList;
	}

	/**
	 * @return string[]
	 */
	public function getLocaleWhiteList(): array
	{
		return $this->localeWhiteList;
	}

	/**
	 * @param string[] $fallbackLocales
	 */
	public function setFallbackLocales(array $fallbackLocales): void
	{
		$this->fallbackLocales = $fallbackLocales;
	}

	public function setCurrentLocale(string $currentLocale): void
	{
		$this->currentLocale = $currentLocale;
	}

	public function getCurrentLocale(): string
	{
		$currentLocale = $this->currentLocale;

		if ($currentLocale === null) {
			$resolved = $this->localeResolver->resolve($this);
			if (LocaleHelper::isWhitelisted($resolved, $this->localeWhiteList)) {
				$currentLocale = $resolved;
			}
		}

		if ($currentLocale === null) {
			$currentLocale = $this->defaultLocale;
		}

		return $currentLocale;
	}

	/**
	 * @return string[]
	 */
	private function getCurrentLocaleList(): array
	{
		if ($this->currentLocaleList !== null) {
			return $this->currentLocaleList;
		}

		$list = [];

		// Add current locale
		$list[] = $current = $this->getCurrentLocale();
		if (($shortCurrent = LocaleHelper::shorten($current)) !== $current) {
			$list[] = $shortCurrent;
		}

		// Add locale from fallback
		if (array_key_exists($current, $this->fallbackLocales)) {
			$list[] = $fallback = $this->fallbackLocales[$current];
			if (($shortFallback = LocaleHelper::shorten($fallback)) !== $fallback) {
				$list[] = $shortFallback;
			}
		}

		if ($current !== $shortCurrent && array_key_exists($shortCurrent, $this->fallbackLocales)) {
			$list[] = $fallback = $this->fallbackLocales[$shortCurrent];
			if (($shortFallback = LocaleHelper::shorten($fallback)) !== $fallback) {
				$list[] = $shortFallback;
			}
		}

		// Add default locale
		$list[] = $default = $this->defaultLocale;
		if (($shortDefault = LocaleHelper::shorten($default)) !== $default) {
			$list[] = $shortDefault;
		}

		// Remove possible duplicates
		$list = array_unique($list);

		$this->currentLocaleList = $list;
		return $list;
	}

}
