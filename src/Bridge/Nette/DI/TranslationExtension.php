<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Bridge\Latte\TranslationFilters;
use Modette\Translation\Bridge\Latte\TranslationMacros;
use Modette\Translation\Bridge\Nette\CacheDI\CachedLazyCatalogue;
use Modette\Translation\Bridge\Nette\Localization\NetteTranslator;
use Modette\Translation\Bridge\Tracy\TranslationPanel;
use Modette\Translation\ConfigurableTranslator;
use Modette\Translation\DefaultTranslator;
use Modette\Translation\Formatting\MessageFormatter;
use Modette\Translation\Formatting\MessageFormatterFactory;
use Modette\Translation\Locale\LocaleHelper;
use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\Resource\Catalogue;
use Modette\Translation\Translator;
use Modette\Translation\TranslatorHolder;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\Localization\ITranslator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpLiteral;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class TranslationExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'cache' => Expect::structure([
				'enabled' => Expect::bool(true), //TODO - cache
			]),
			'debug' => Expect::structure([
				'panel' => Expect::bool(false),
			]),
			'holder' => Expect::structure([
				'enabled' => Expect::bool(true),
			]),
			'locale' => Expect::structure([
				'default' => Expect::string()->required(),
				'whitelist' => Expect::listOf('string'),
				'fallback' => Expect::arrayOf('string'),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		LocaleHelper::validate($config->locale->default);

		foreach ($config->locale->whitelist as $whitelistedLocale) {
			LocaleHelper::validate($whitelistedLocale);
		}

		foreach ($config->locale->fallback as $requestedLocale => $fallbackLocale) {
			LocaleHelper::validate($requestedLocale);
			LocaleHelper::validate($fallbackLocale);
		}

		$resolverNames = []; //TODO - resolvers
		// TODO - loaders, catalogue, commands, generators

		$resolverDefinition = $builder->addDefinition($this->prefix('resolver'))
			->setFactory(LazyMultiLocaleResolver::class, ['@container', $resolverNames])
			->setType(LocaleResolver::class)
			->setAutowired(false);

		$catalogueDefinition = $builder->addDefinition($this->prefix('catalogue'))
			->setFactory(CachedLazyCatalogue::class)
			->setType(Catalogue::class)
			->setAutowired(false);

		$messageFormatterDefinition = $builder->addDefinition($this->prefix('formatter'))
			->setFactory('?::create()', [MessageFormatterFactory::class])
			->setType(MessageFormatter::class)
			->setAutowired(false);

		$loggerDefinition = $builder->addDefinition($this->prefix('logger'))
			->setFactory(TranslationsLogger::class)
			->setType(TranslationsLogger::class)
			->setAutowired(false);

		$translatorPrefix = $this->prefix('translator');
		$translatorDefinition = $builder->addDefinition($translatorPrefix)
			->setFactory(
				'?::fromValidLocales(?, ?, ?, ?, ?, ?)',
				[
					DefaultTranslator::class,
					$config->locale->default,
					$config->locale->whitelist,
					$config->locale->fallback,
					$resolverDefinition,
					$catalogueDefinition,
					$messageFormatterDefinition,
					$loggerDefinition,
				]
			)
			->setType(Translator::class)
			->setAutowired([Translator::class, ConfigurableTranslator::class]);

		$builder->addDefinition($this->prefix('translator.nette'))
			->setFactory(NetteTranslator::class, [$translatorDefinition])
			->setType(ITranslator::class);

		if ($config->holder->enabled) {
			$builder->addDefinition($this->prefix('translator.lazy'))
				->setFactory(LazyTranslator::class, ['@container', $translatorPrefix])
				->setType(Translator::class)
				->setAutowired(false);
		}

		if ($config->debug->panel) {
			$builder->addDefinition($this->prefix('tracy.panel'))
				->setFactory(TranslationPanel::class, [$translatorDefinition, $loggerDefinition])
				->setType(TranslationPanel::class)
				->setAutowired(false);
		}

		$latteFactoryName = $builder->getByType(ILatteFactory::class);
		if ($latteFactoryName !== null) {
			$latteFactoryDefinition = $builder->getDefinition($latteFactoryName);
			assert($latteFactoryDefinition instanceof FactoryDefinition);

			$latteFiltersDefinition = $builder->addDefinition($this->prefix('latte.filters'))
				->setFactory(TranslationFilters::class)
				->setType(TranslationFilters::class)
				->setAutowired(false);

			$latteFactoryDefinition->getResultDefinition()
				->addSetup('?->onCompile[] = function($engine) { ?::install($engine->getCompiler()); }', ['@self', new PhpLiteral(TranslationMacros::class)])
				->addSetup('?->addProvider(?, ?)', ['@self', 'translator', $translatorDefinition])
				->addSetup('?->addFilter', ['@self', 'translate', [$latteFiltersDefinition, 'translate']]);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;
		$initialize = $class->getMethod('initialize');

		if ($config->debug->panel) {
			$initialize->addBody('$this->getService(?)->addPanel($this->getService(?));', [
				'tracy.bar',
				$this->prefix('tracy.panel'),
			]);
		}

		if ($config->holder->enabled) {
			$initialize->addBody('?::setTranslator(?);', [TranslatorHolder::class, $this->prefix('translator.lazy')]);
		}
	}

}
