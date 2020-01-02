<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

use Modette\Translation\ConfigurableTranslator;

/**
 * Calls to some configurators (cookies) don't appear immediately in corresponding resolver
 * This bridge set locale as current translator locale to bypass the problem
 */
final class TranslatorLocaleConfigurator implements LocaleConfigurator
{

	/** @var ConfigurableTranslator */
	private $translator;

	/** @var LocaleConfigurator */
	private $wrappedConfigurator;

	public function __construct(ConfigurableTranslator $translator, LocaleConfigurator $wrappedConfigurator)
	{
		$this->translator = $translator;
		$this->wrappedConfigurator = $wrappedConfigurator;
	}

	public function configure(string $locale): void
	{
		$this->wrappedConfigurator->configure($locale);
		$this->translator->setCurrentLocale($locale);
	}

}
