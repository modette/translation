<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Resource\Catalogue;
use Modette\Translation\Resource\Loader;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\DI\Container;

final class CachedLazyCatalogue implements Catalogue
{

	private const CACHE_KEY = 'modette.translation';

	/** @var Cache */
	private $cache;

	/** @var Container */
	private $container;

	/** @var string[] */
	private $loaderNames;

	/** @var Loader[] */
	private $loadersMap = [];

	/** @var bool[][] Map of loaderName => locale which were fully parsed and can be skipped */
	private $processedLoaderLocaleMap = [];

	/** @var bool[][] Map of message => locale which were not found in any loader */
	private $missingTranslationMessageLocaleMap = [];

	/**
	 * @param IStorage $storage
	 * @param string[] $loaderNames
	 */
	public function __construct(IStorage $storage, Container $container, array $loaderNames)
	{
		$this->cache = new Cache($storage, self::CACHE_KEY);
		$this->container = $container;
		$this->loaderNames = $loaderNames;
	}

	/**
	 * @param mixed[]  $parameters
	 * @param string[] $localeList
	 */
	public function getMessage(string $message, array $localeList): ?string
	{
		$preferredLocale = reset($localeList); // Get first locale from list
		$cache = $this->cache->derive('.' . $preferredLocale);

		$translated = $this->cache->load($message);

		// Translation is already cached
		if ($translated !== null) {
			return $translated;
		}

		// None of loaders contains translation for given message with requested language, skip lookup
		//TODO - přenést před načítání z cache? (ale musí se použít array_key_exist, takhle by to jinak vždy vrátilo null
		if ($this->missingTranslationMessageLocaleMap[$message][$preferredLocale] ?? false) {
			return null;
		}

		// Load translations from all loaders and all possible locales until message translation is found
		foreach ($this->loaderNames as $loaderName) {
			// Loader translations for requested language already stored, skip
			if ($this->processedLoaderLocaleMap[$loaderName][$preferredLocale] ?? false) {
				continue;
			}

			$loader = $this->getLoader($loaderName);

			foreach ($localeList as $locale) {
				foreach ($loader->loadAllMessages($locale) as $key => $translation) {
					//TODO - následující se vylučují
					// - cache se ukládá jen pro hlavní jazyk - ukládat i pro načtené alternativy? (vytvořit derivát cache, zanést loader do mapy pro oba jazyky)
					// - obalit loader do kešovacího array loaderu? loader se bude volat víckrát za request, pokud se budou načítat překlady pro více $preferredLanguage
					$cache->save($key, $translation);

					// Loaded key is same as requested message, use it
					if ($key === $message) {
						$translated = $translation;
					}
				}

				// Loader translations for requested language stored, skip at next run
				$this->processedLoaderLocaleMap[$loaderName][$preferredLocale] = true;

				// All messages from given loader for current language are loaded and translation was found - skip other until needed
				if ($translated !== null) {
					return $translated;
				}
			}
		}

		// None of loaders contains translation for given message with requested language, skip at next run
		if ($translated === null) {
			$this->missingTranslationMessageLocaleMap[$message][$preferredLocale] = true;
		}

		return $translated;
	}

	private function getLoader(string $loaderName): Loader
	{
		if (!array_key_exists($loaderName, $this->loadersMap)) {
			$loader = $this->container->getService($loaderName);
			assert($loader instanceof Loader);
			$this->loadersMap[$loaderName] = $loader;
		}

		return $this->loadersMap[$loaderName];
	}

}
