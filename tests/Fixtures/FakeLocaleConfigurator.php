<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Fixtures;

use Modette\Translation\Locale\LocaleConfigurator;

final class FakeLocaleConfigurator implements LocaleConfigurator
{

	/** @var string|null */
	private $locale;

	public function configure(string $locale): void
	{
		$this->locale = $locale;
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

}
