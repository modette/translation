<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\CacheDI;

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
	private $loaderServiceNames;

	/** @var Loader[] */
	private $loadersMap = [];

	/** @var bool[][] Map of loaderName => locale which were fully parsed and can be skipped */
	private $processedLoaderLocaleMap = [];

	/** @var bool[][] Map of message => locale which were not found in any loader */
	private $missingTranslationMessageLocaleMap = [];

	/**
	 * @param string[] $loaderServiceNames
	 */
	public function __construct(IStorage $storage, Container $container, array $loaderServiceNames)
	{
		$this->cache = new Cache($storage, self::CACHE_KEY);
		$this->container = $container;
		$this->loaderServiceNames = $loaderServiceNames;
	}

	public function getMessage(string $message, string $locale): ?string
	{
		$cache = $this->cache->derive('.' . $locale);

		// Try get translation from cache
		$translated = $this->cache->load($message);

		// Translation is already cached
		if ($translated !== null) {
			return $translated;
		}

		// None of loaders contains translation for given message with requested language, skip lookup
		if ($this->missingTranslationMessageLocaleMap[$message][$locale] ?? false) {
			return null;
		}

		// Load translations from all loaders and all possible locales until message translation is found
		//TODO - pouze jeden loader
		foreach ($this->loaderServiceNames as $loaderServiceName) {
			// Loader translations for requested language already stored, skip
			if ($this->processedLoaderLocaleMap[$loaderServiceName][$locale] ?? false) {
				continue;
			}

			$loader = $this->getLoader($loaderServiceName);

			foreach ($loader->loadAllMessages($locale) as $key => $translation) {
				//TODO - obalit loader do kešovacího array loaderu? loader se bude volat víckrát za request, pokud se budou načítat překlady pro více $locale
				//TODO - při přidání nového překladu se cache pokusí uložit znova
				$cache->save($key, $translation);

				// Loaded key is same as requested message, use it
				if ($key === $message) {
					$translated = $translation;
				}
			}

			// Loader translations for requested language stored, skip at next run
			$this->processedLoaderLocaleMap[$loaderServiceName][$locale] = true;

			// All messages from given loader for current language are loaded and translation was found - skip other until needed
			if ($translated !== null) {
				return $translated;
			}
		}

		// None of loaders contains translation for given message with requested language, skip at next run
		$this->missingTranslationMessageLocaleMap[$message][$locale] = true;

		return null;
	}

	private function getLoader(string $loaderServiceName): Loader
	{
		if (!array_key_exists($loaderServiceName, $this->loadersMap)) {
			$loader = $this->container->getService($loaderServiceName);
			assert($loader instanceof Loader);
			$this->loadersMap[$loaderServiceName] = $loader;
		}

		return $this->loadersMap[$loaderServiceName];
	}

}
