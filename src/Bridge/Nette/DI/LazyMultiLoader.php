<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Resource\Loader;
use Nette\DI\Container;

final class LazyMultiLoader implements Loader
{

	/** @var string[] */
	private $loaderServiceNames;

	/** @var Container */
	private $container;

	/** @var Loader[] */
	private $loaderMap = [];

	/**
	 * @param string[] $loaderServiceNames
	 */
	public function __construct(array $loaderServiceNames, Container $container)
	{
		$this->loaderServiceNames = $loaderServiceNames;
		$this->container = $container;
	}

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array
	{
		$messagesByLoader = [];

		foreach ($this->loaderServiceNames as $loaderServiceName) {
			$loader = $this->getLoader($loaderServiceName);

			$messagesByLoader[] = $loader->loadAllMessages($locale);
		}

		return $messagesByLoader === [] ? [] : array_merge(...$messagesByLoader);
	}

	private function getLoader(string $loaderServiceName): Loader
	{
		if (!isset($this->loaderMap[$loaderServiceName])) {
			$loader = $this->container->getService($loaderServiceName);
			assert($loader instanceof Loader);
			$this->loaderMap[$loaderServiceName] = $loader;
		}

		return $this->loaderMap[$loaderServiceName];
	}

}
