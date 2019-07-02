<?php declare(strict_types = 1);

namespace Modette\Translation;

interface ConfigurableTranslator extends Translator
{

	public function setDefaultLocale(string $defaultLocale): void;

	/**
	 * @param string[] $localeWhitelist
	 */
	public function setLocaleWhitelist(array $localeWhitelist): void;

	/**
	 * @param string[] $fallbackLocales
	 */
	public function setFallbackLocales(array $fallbackLocales): void;

}
