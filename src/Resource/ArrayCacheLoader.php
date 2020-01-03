<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

/**
 * Ensures wrapped loader is called only once for every used locale
 */
final class ArrayCacheLoader implements Loader
{

	/** @var Loader */
	private $loader;

	/** @var string[][] */
	private $cache = [];

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array
	{
		if (isset($this->cache[$locale])) {
			return $this->cache[$locale];
		}

		return $this->cache[$locale] = $this->loader->loadAllMessages($locale);
	}

}
