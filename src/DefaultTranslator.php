<?php declare(strict_types = 1);

namespace Modette\Translation;

use Modette\Exceptions\Logic\InvalidStateException;
use Modette\Translation\Exception\LocaleNotWhitelistedException;
use Modette\Translation\Formatting\MessageFormatter;
use Modette\Translation\Locale\LocaleHelper;
use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\Resource\Catalogue;

final class DefaultTranslator implements ConfigurableTranslator
{

	/** @var string */
	private $defaultLocale;

	/** @var string[] */
	private $localeWhitelist;

	/** @var string[] */
	private $fallbackLocales;

	/** @var LocaleResolver */
	private $localeResolver;

	/** @var Catalogue */
	private $catalogue;

	/** @var MessageFormatter */
	private $messageFormatter;

	/** @var TranslationsLogger */
	private $logger;

	/** @var string|null */
	private $currentLocale;

	/** @var string[][] */
	private $possibleLocales = [];

	/**
	 * @param string[] $localeWhiteList
	 * @param string[] $fallbackLocales
	 */
	private function __construct(
		string $defaultLocale,
		array $localeWhiteList,
		array $fallbackLocales,
		LocaleResolver $localeResolver,
		Catalogue $catalogue,
		MessageFormatter $messageFormatter,
		TranslationsLogger $logger
	)
	{
		$this->defaultLocale = $defaultLocale;
		$this->localeWhitelist = $localeWhiteList;
		$this->fallbackLocales = $fallbackLocales;
		$this->localeResolver = $localeResolver;
		$this->catalogue = $catalogue;
		$this->messageFormatter = $messageFormatter;
		$this->logger = $logger;
	}

	/**
	 * @param string[] $localeWhiteList
	 * @param string[] $fallbackLocales
	 */
	public static function fromValidLocales(
		string $defaultLocale,
		array $localeWhiteList,
		array $fallbackLocales,
		LocaleResolver $localeResolver,
		Catalogue $catalogue,
		MessageFormatter $messageFormatter,
		TranslationsLogger $logger
	): self
	{
		return new self($defaultLocale, $localeWhiteList, $fallbackLocales, $localeResolver, $catalogue, $messageFormatter, $logger);
	}

	/**
	 * @param string[] $localeWhiteList
	 * @param string[] $fallbackLocales
	 */
	public static function fromRawLocales(
		string $defaultLocale,
		array $localeWhiteList,
		array $fallbackLocales,
		LocaleResolver $localeResolver,
		Catalogue $catalogue,
		MessageFormatter $messageFormatter,
		TranslationsLogger $logger
	): self
	{
		LocaleHelper::validate($defaultLocale);

		foreach ($localeWhiteList as $whitelistedLocale) {
			LocaleHelper::validate($whitelistedLocale);
		}

		foreach ($fallbackLocales as $requestedLocale => $fallbackLocale) {
			LocaleHelper::validate($requestedLocale);
			LocaleHelper::validate($fallbackLocale);
		}

		return new self($defaultLocale, $localeWhiteList, $fallbackLocales, $localeResolver, $catalogue, $messageFormatter, $logger);
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function translate(string $message, array $parameters = [], ?string $locale = null): string
	{
		if ($locale !== null) {
			$this->checkValidAndWhitelisted($locale);
		}

		$locale = $locale ?? $this->getCurrentLocale();
		$possibleLocales = $this->getPossibleLocales($locale);

		// Should not happen, foreach should always have at least one iteration
		$translatedMessage = null;
		$messageLocale = $locale;

		foreach ($possibleLocales as $messageLocale) {
			$translatedMessage = $this->catalogue->getMessage($message, $messageLocale);

			if ($translatedMessage !== null) {
				break;
			}
		}

		if ($translatedMessage === null) {
			$this->logger->addMissingResource($locale, $message);

			return $message;
		}

		return $this->messageFormatter->formatMessage($messageLocale, $translatedMessage, $parameters);
	}

	public function getDefaultLocale(): string
	{
		return $this->defaultLocale;
	}

	/**
	 * @return string[]
	 */
	public function getLocaleWhitelist(): array
	{
		return $this->localeWhitelist;
	}

	public function setCurrentLocale(string $locale): void
	{
		if ($this->currentLocale !== null) {
			throw new InvalidStateException(sprintf(
				'Current locale already set. Ensure you call %s::%s only once and before translator is first used.',
				ConfigurableTranslator::class,
				__METHOD__
			));
		}

		$this->checkValidAndWhitelisted($locale);
		$this->currentLocale = $locale;
	}

	public function getCurrentLocale(): string
	{
		if ($this->currentLocale !== null) {
			return $this->currentLocale;
		}

		$resolved = $this->localeResolver->resolve($this->localeWhitelist);

		if ($resolved !== null) {
			$resolved = LocaleHelper::normalize($resolved);

			if (LocaleHelper::isWhitelisted($resolved, $this->localeWhitelist)) {
				return $this->currentLocale = $resolved;
			}
		}

		return $this->currentLocale = $this->defaultLocale;
	}

	private function checkValidAndWhitelisted(string $locale): void
	{
		LocaleHelper::validate($locale);

		if (!LocaleHelper::isWhitelisted($locale, $this->localeWhitelist)) {
			throw LocaleNotWhitelistedException::forWhitelist($locale, $this->localeWhitelist);
		}
	}

	/**
	 * @return string[]
	 */
	private function getPossibleLocales(string $requestedLocale): array
	{
		if (isset($this->possibleLocales[$requestedLocale])) {
			return $this->possibleLocales[$requestedLocale];
		}

		$list = [];

		// Add requested locale
		$list[] = $requestedLocale;
		if (($shortRequestedLocale = LocaleHelper::shorten($requestedLocale)) !== $requestedLocale) {
			$list[] = $shortRequestedLocale;
		}

		// Add locale from fallback
		if (isset($this->fallbackLocales[$requestedLocale])) {
			$list[] = $fallback = $this->fallbackLocales[$requestedLocale];
			if (($shortFallback = LocaleHelper::shorten($fallback)) !== $fallback) {
				$list[] = $shortFallback;
			}
		}

		// Add short locale from fallback
		if ($requestedLocale !== $shortRequestedLocale && isset($this->fallbackLocales[$shortRequestedLocale])) {
			$list[] = $fallback = $this->fallbackLocales[$shortRequestedLocale];
			if (($shortFallback = LocaleHelper::shorten($fallback)) !== $fallback) {
				$list[] = $shortFallback;
			}
		}

		// Add default locale
		$list[] = $default = $this->defaultLocale;
		if (($shortDefault = LocaleHelper::shorten($default)) !== $default) {
			$list[] = $shortDefault;
		}

		// Remove duplicates
		$list = array_unique($list);

		$this->possibleLocales[$requestedLocale] = $list;
		return $list;
	}

}
