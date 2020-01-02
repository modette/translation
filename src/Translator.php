<?php declare(strict_types = 1);

namespace Modette\Translation;

interface Translator
{

	/**
	 * @param mixed[] $parameters
	 */
	public function translate(string $message, array $parameters = [], ?string $locale = null): string;

	public function getCurrentLocale(): string;

	public function getDefaultLocale(): string;

	/**
	 * @return string[]
	 */
	public function getLocaleWhitelist(): array;

}
