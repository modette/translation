<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Bridge\Nette\DI;

use Modette\Translation\Bridge\Latte\TranslationFilters;
use Modette\Translation\Bridge\Nette\Caching\CachedCatalogue;
use Modette\Translation\Bridge\Nette\DI\LazyMultiLoader;
use Modette\Translation\Bridge\Nette\DI\LazyMultiLocaleResolver;
use Modette\Translation\Bridge\Nette\DI\LazyTranslator;
use Modette\Translation\Bridge\Nette\Localization\NetteTranslator;
use Modette\Translation\Bridge\Tracy\TranslationPanel;
use Modette\Translation\DefaultTranslator;
use Modette\Translation\Formatting\MessageFormatter;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\Resource\ArrayCacheCatalogue;
use Modette\Translation\Resource\ArrayCacheLoader;
use Modette\Translation\Translator;
use Modette\Translation\TranslatorHolder;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Extensions\ExtensionsExtension;
use PHPUnit\Framework\TestCase;
use function Modette\Translation\__;

/**
 * @runTestsInSeparateProcesses
 */
final class TranslationExtensionTest extends TestCase
{

	private const TEMP_PATH = __DIR__ . '/../../../../../var/tmp';

	public function testMinimal(): void
	{
		$loader = new ContainerLoader(self::TEMP_PATH . '/cache', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('extensions', new ExtensionsExtension());
			$compiler->loadConfig(__DIR__ . '/config.minimal.neon');
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);
		$container->initialize();

		$translator = $container->getByType(Translator::class);
		self::assertInstanceOf(DefaultTranslator::class, $translator);

		self::assertSame('en', $translator->getDefaultLocale());
		self::assertSame(['en'], $translator->getLocaleWhitelist());
		self::assertSame('en', $translator->getCurrentLocale());

		self::assertInstanceOf(LazyMultiLocaleResolver::class, $container->getService('modette.translation.resolver'));
		self::assertInstanceOf(LazyMultiLoader::class, $container->getService('modette.translation.loader'));
		self::assertInstanceOf(ArrayCacheLoader::class, $container->getService('modette.translation.loader.cache'));
		self::assertInstanceOf(CachedCatalogue::class, $container->getService('modette.translation.catalogue'));
		self::assertInstanceOf(ArrayCacheCatalogue::class, $container->getService('modette.translation.catalogue.cache'));
		self::assertInstanceOf(MessageFormatter::class, $container->getService('modette.translation.formatter'));
		self::assertInstanceOf(TranslationsLogger::class, $container->getService('modette.translation.logger'));
		self::assertInstanceOf(DefaultTranslator::class, $container->getService('modette.translation.translator'));
		self::assertInstanceOf(NetteTranslator::class, $container->getService('modette.translation.translator.nette'));
		self::assertInstanceOf(LazyTranslator::class, $container->getService('modette.translation.translator.lazy'));
		self::assertFalse($container->hasService('modette.translation.tracy.panel'));
		self::assertFalse($container->hasService('modette.translation.latte.filters'));
		self::assertInstanceOf(LazyTranslator::class, TranslatorHolder::getInstance()->getTranslator());
		self::assertSame('test', __('test'));
	}

	public function testFull(): void
	{
		$loader = new ContainerLoader(self::TEMP_PATH . '/cache', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('extensions', new ExtensionsExtension());
			$compiler->addConfig([
				'parameters' => [
					'tempDir' => self::TEMP_PATH,
				],
			]);
			$compiler->loadConfig(__DIR__ . '/config.full.neon');
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);
		$container->initialize();

		$translator = $container->getByType(Translator::class);
		self::assertInstanceOf(DefaultTranslator::class, $translator);

		self::assertSame('en', $translator->getDefaultLocale());
		self::assertSame(['cs', 'fr', 'de', 'sk', 'en'], $translator->getLocaleWhitelist());
		self::assertSame('en', $translator->getCurrentLocale());

		self::assertInstanceOf(LazyMultiLocaleResolver::class, $container->getService('modette.translation.resolver'));
		self::assertInstanceOf(LazyMultiLoader::class, $container->getService('modette.translation.loader'));
		self::assertInstanceOf(ArrayCacheLoader::class, $container->getService('modette.translation.loader.cache'));
		self::assertInstanceOf(CachedCatalogue::class, $container->getService('modette.translation.catalogue'));
		self::assertInstanceOf(ArrayCacheCatalogue::class, $container->getService('modette.translation.catalogue.cache'));
		self::assertInstanceOf(MessageFormatter::class, $container->getService('modette.translation.formatter'));
		self::assertInstanceOf(TranslationsLogger::class, $container->getService('modette.translation.logger'));
		self::assertInstanceOf(DefaultTranslator::class, $container->getService('modette.translation.translator'));
		self::assertInstanceOf(NetteTranslator::class, $container->getService('modette.translation.translator.nette'));
		self::assertFalse($container->hasService('modette.translation.translator.lazy'));
		self::assertInstanceOf(TranslationPanel::class, $container->getService('modette.translation.tracy.panel'));
		self::assertInstanceOf(TranslationFilters::class, $container->getService('modette.translation.latte.filters'));

		//TODO - fallbacks, loaders, resolvers, configurators
		// 	   - logger
	}

}
