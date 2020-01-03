<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

/**
 * Ensures wrapped catalogue is called only once for every message
 */
final class ArrayCacheCatalogue implements Catalogue
{

	/** @var Catalogue */
	private $catalogue;

	/** @var (string|null)[][] */
	private $cache = [];

	public function __construct(Catalogue $catalogue)
	{
		$this->catalogue = $catalogue;
	}

	public function getMessage(string $message, string $locale): ?string
	{
		if (isset($this->cache[$message]) && array_key_exists($locale, $this->cache[$message])) {
			return $this->cache[$message][$locale];
		}

		return $this->cache[$message][$locale] = $this->catalogue->getMessage($message, $locale);
	}

}
