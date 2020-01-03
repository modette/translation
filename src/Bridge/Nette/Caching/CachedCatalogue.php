<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Caching;

use Modette\Translation\Resource\Catalogue;
use Modette\Translation\Resource\Loader;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

final class CachedCatalogue implements Catalogue
{

	private const CACHE_KEY = 'modette.translation';

	/** @var Loader */
	private $loader;

	/** @var Cache */
	private $cache;

	/** @var bool[][] Map of message => locale which were not found in any loader */
	private $missingTranslationLocaleMap = [];

	public function __construct(Loader $loader, IStorage $storage)
	{
		$this->loader = $loader;
		$this->cache = new Cache($storage, self::CACHE_KEY);
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

		// Loader don't contain translation for given message with requested language, skip lookup
		if ($this->missingTranslationLocaleMap[$message][$locale] ?? false) {
			return null;
		}

		// Load all translations for given locale
		foreach ($this->loader->loadAllMessages($locale) as $key => $translation) {
			// Try to load message and save only if not cached yet
			$cache->load($key, static function () use ($translation): string {
				return $translation;
			});

			// Loaded key is same as requested message, use it
			if ($key === $message) {
				$translated = $translation;
			}
		}

		// Translation found, return it
		if ($translated !== null) {
			return $translated;
		}

		// Loader don't contain translation for given message with requested language, skip at next run
		$this->missingTranslationLocaleMap[$message][$locale] = true;

		return null;
	}

}
