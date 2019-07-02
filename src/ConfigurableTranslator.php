<?php declare(strict_types = 1);

namespace Modette\Translation;

interface ConfigurableTranslator extends Translator
{

	public function setDefaultLocale(string $defaultLocale): void;

	public function setCurrentLocale(string $currentLocale): void;

	/**
	 * @param string[] $localeWhitelist
	 */
	public function setLocaleWhiteList(array $localeWhitelist): void;

	/**
	 * @param string[] $fallbackLocales
	 */
	public function setFallbackLocales(array $fallbackLocales): void;

}
