<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Bridge\Latte\TranslationFilters;
use Modette\Translation\Bridge\Latte\TranslationMacros;
use Modette\Translation\Bridge\Nette\Localization\NetteTranslator;
use Modette\Translation\Bridge\Tracy\TranslationPanel;
use Modette\Translation\DefaultTranslator;
use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Translator;
use Modette\Translation\TranslatorHolder;
use Modette\Translation\Utils\LocaleHelper;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\ServiceDefinition;
use Nette\Localization\ITranslator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpLiteral;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Tracy\Bar;

/**
 * @property-read stdClass $config
 */
final class TranslatorExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'cache' => Expect::structure([
				'enabled' => Expect::bool(true), //TODO - cache
			]),
			'debug' => Expect::structure([
				'panel' => Expect::bool(false),
				'missingResource' => Expect::anyOf('throw', 'log', 'ignore'),
			]),
			'holder' => Expect::structure([
				'enabled' => Expect::bool(false),
			]),
			'locale' => Expect::structure([
				'default' => Expect::string(),
				'fallback' => Expect::arrayOf('string'),
				'whitelist' => Expect::listOf('string'),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		// TODO
		// - pro whitelist a fallback by se hodila hierarchická struktura pro rychlejší lookup
		//
		// TODO
		//  missingResource
		//   throw - vyhodí exception
		//   log - zaloguje a vrátí zástupný string
		//   ignore - vrátí zástupný string
		//
		// TODO
		//  chtělo by to rozlišovat přes jednu proměnou (debugMode)
		//  v debug módu chci logovat i do panelu
		//  chování se může různit pro překlad dostupný ve výchozím jazyku a pro kompletně chybějící

		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$resolverNames = []; //TODO - resolvers

		$resolverDefinition = $builder->addDefinition($this->prefix('resolver'))
			->setType(LocaleResolver::class)
			->setFactory(LazyMultiLocaleResolver::class, ['@container', $resolverNames]);

		$translatorPrefix = $this->prefix('translator');
		$translatorDefinition = $builder->addDefinition($translatorPrefix)
			->setType(Translator::class)
			->setFactory(DefaultTranslator::class, [$resolverDefinition]);

		$defaultLocale = $config->locale->default;
		if ($defaultLocale !== null) {
			LocaleHelper::checkValid($defaultLocale);
			$translatorDefinition->addSetup('setDefaultLocale', [$defaultLocale]);
		}

		$whitelist = $config->locale->whitelist;
		foreach ($whitelist as $locale) {
			LocaleHelper::checkValid($locale);
		}
		$translatorDefinition->addSetup('setLocaleWhitelist', [$whitelist]);

		$fallback = $config->locale->fallback;
		foreach ($fallback as $given => $substitute) {
			LocaleHelper::checkValid($given);
			LocaleHelper::checkValid($substitute);
		}
		$translatorDefinition->addSetup('setFallbackLocales', [$fallback]);

		$netteTranslatorDefinition = $builder->addDefinition($this->prefix('translator.nette'))
			->setType(ITranslator::class)
			->setFactory(NetteTranslator::class, [$translatorDefinition]);

		if ($config->holder->enabled) {
			$builder->addDefinition($this->prefix('translator.lazy'))
				->setType(Translator::class)
				->setFactory(LazyTranslator::class, ['@container', $translatorPrefix])
				->setAutowired(false);
		}

		if ($config->debug->panel) {
			$builder->addDefinition($this->prefix('tracy.panel'))
				->setType(TranslationPanel::class)
				->setFactory(TranslationPanel::class)
				->setAutowired(false);
		}

		$latteFactoryName = $builder->getByType(ILatteFactory::class);
		if ($latteFactoryName !== null) {
			$latteFactoryDefinition = $builder->getDefinition($latteFactoryName);
			assert($latteFactoryDefinition instanceof FactoryDefinition);

			$latteFiltersDefinition = $builder->addDefinition($this->prefix('latte.filters'))
				->setType(TranslationFilters::class)
				->setFactory(TranslationFilters::class)
				->setAutowired(false);

			$latteFactoryDefinition->getResultDefinition()
				->addSetup('?->onCompile[] = function($engine) { ?::install($engine->getCompiler()); }', ['@self', new PhpLiteral(TranslationMacros::class)])
				->addSetup('addProvider(?, ?)', ['translator', $netteTranslatorDefinition])
				->addSetup('addFilter', ['translate', [$latteFiltersDefinition, 'translate']]);
		}
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		if ($config->debug->panel) {
			$tracyBarDefinition = $builder->getDefinitionByType(Bar::class);
			assert($tracyBarDefinition instanceof ServiceDefinition);
			$tracyBarDefinition->addSetup('addPanel(?)', [$this->prefix('tracy.panel')]);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;

		if ($config->holder->enabled) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(TranslatorHolder::class . '::setTranslator(?);', [$this->prefix('translator.lazy')]);
		}
	}

}
