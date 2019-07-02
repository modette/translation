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
	private $localeWhitelist;

	/** @var string[] */
	private $fallbackLocales;

	/** @var string|null */
	private $currentLocale;

	/** @var string[]|null */
	private $currentFallbackLocales;

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
		if (($locale !== null) && !LocaleHelper::isWhitelisted($locale, $this->localeWhitelist)) {
			throw new InvalidArgumentException(sprintf('Locale "%s" is not whitelisted.', $locale));
		}

		$locale = $locale ?? $this->getCurrentLocale();
		$localeList = $this->getCurrentFallbackLocales();

		//TODO
		// hodnoty z configuratoru rovnou nastavit do translatoru, aby byl aktuální?

		//TODO - v loaderech validovat, že všechny překlady jsou string

		//TODO - měla by existovat i nějaká možnost invalidace - pro vývoj i runtime (databázové překlady)

		$translatedMessage = $this->catalogue->getMessage($message, $localeList);
		if ($translatedMessage === null) {
			//TODO - debug, logging - no translation for message found in catalogue
			return $message;
		}

		//TODO - formátování
		// vyžadovat ext-intl nebo mít fallback
		//  http://userguide.icu-project.org/formatparse/messages
		//  https://www.sitepoint.com/localization-demystified-understanding-php-intl/
		//  https://github.com/Magneds/php-messageformat
		//  https://github.com/symfony/polyfill-intl-messageformatter/blob/master/MessageFormatter.php
		return MessageFormatter::formatMessage($locale, $translatedMessage, $parameters);
	}

	public function setDefaultLocale(string $defaultLocale): void
	{
		$this->defaultLocale = $defaultLocale;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function setLocaleWhitelist(array $localeWhitelist): void
	{
		$this->localeWhitelist = $localeWhitelist;
	}

	/**
	 * @param string[] $fallbackLocales
	 */
	public function setFallbackLocales(array $fallbackLocales): void
	{
		$this->fallbackLocales = $fallbackLocales;
	}

	private function getCurrentLocale(): string
	{
		$currentLocale = $this->currentLocale;

		if ($currentLocale === null) {
			$resolved = $this->localeResolver->resolve();
			if (LocaleHelper::isWhitelisted($resolved, $this->localeWhitelist)) {
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
	private function getCurrentFallbackLocales(): array
	{
		if ($this->currentFallbackLocales !== null) {
			return $this->currentFallbackLocales;
		}

		$list = [];

		$list[] = $current = $this->getCurrentLocale();
		if (($shortCurrent = LocaleHelper::shorten($current)) !== $current) {
			$list[] = $shortCurrent;
		}

		// TODO - fallback
		//  sk => cs
		//  en-GB => en-US
		//
		//TODO - vrátit seznam všech přijatelných fallbacků pro aktuální jazyk
		// pro každý long formát se přidá automaticky short formát
		if (array_key_exists($current, $this->fallbackLocales)) {
			//TODO - vypočítat short fallback
			$fallback = $this->fallbackLocales[$current];
		}

		if ($current !== $shortCurrent && array_key_exists($shortCurrent, $this->fallbackLocales)) {
			//TODO - vypočítat short fallback ze short current a ověřit, že ani jeden z nich už není obsažen v $list
		}

		$list[] = $default = $this->defaultLocale;
		if (($shortDefault = LocaleHelper::shorten($default)) !== $default) {
			$list[] = $shortDefault;
		}

		$list = array_unique($list);
		$this->currentFallbackLocales = $list;
		return $list;
	}

}
