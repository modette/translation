<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

use Modette\Translation\ConfigurableTranslator;

/**
 * Calls to some configurators (CookieLocaleConfigurator) don't appear immediately in corresponding resolver
 * This bridge set locale also as current translator locale to bypass the problem
 * It could override current locale if it was already computed by resolvers
 */
final class TranslatorBridgeConfigurator implements LocaleConfigurator
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
