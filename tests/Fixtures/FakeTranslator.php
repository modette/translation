<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Fixtures;

use Modette\Translation\ConfigurableTranslator;

final class FakeTranslator implements ConfigurableTranslator
{

	/** @var string */
	private $defaultLocale;

	/** @var string */
	private $currentLocale;

	/** @var string[] */
	private $whitelist;

	/**
	 * @param string[] $whitelist
	 */
	public function __construct(string $defaultLocale, array $whitelist = [])
	{
		$this->defaultLocale = $defaultLocale;
		$this->currentLocale = $defaultLocale;
		$this->whitelist = $whitelist;
	}

	public function setCurrentLocale(string $currentLocale): void
	{
		$this->currentLocale = $currentLocale;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function translate(string $message, array $parameters = [], ?string $locale = null): string
	{
		return $message;
	}

	public function getCurrentLocale(): string
	{
		return $this->currentLocale;
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
		return $this->whitelist;
	}

}
