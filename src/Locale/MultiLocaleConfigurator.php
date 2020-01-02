<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

final class MultiLocaleConfigurator implements LocaleConfigurator
{

	/** @var LocaleConfigurator[] */
	private $configurators;

	/**
	 * @param LocaleConfigurator[] $configurators
	 */
	public function __construct(array $configurators)
	{
		$this->configurators = $configurators;
	}

	public function configure(string $locale): void
	{
		foreach ($this->configurators as $configurator) {
			$configurator->configure($locale);
		}
	}

}
