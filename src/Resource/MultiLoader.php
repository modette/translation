<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

final class MultiLoader implements Loader
{

	/** @var Loader[] */
	private $loaders;

	/**
	 * @param Loader[] $loaders
	 */
	public function __construct(array $loaders)
	{
		$this->loaders = $loaders;
	}

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array
	{
		$messagesByLoader = [];

		foreach ($this->loaders as $loader) {
			$messagesByLoader[] = $loader->loadAllMessages($locale);
		}

		return $messagesByLoader === [] ? [] : array_merge(...$messagesByLoader);
	}

}
