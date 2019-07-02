<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Locale\LocaleResolver;
use Nette\DI\Container;

final class LazyMultiLocaleResolver implements LocaleResolver
{

	/** @var Container */
	private $container;

	/** @var string[] */
	private $serviceNames;

	/**
	 * @param string[] $serviceNames
	 */
	public function __construct(Container $container, array $serviceNames)
	{
		$this->container = $container;
		$this->serviceNames = $serviceNames;
	}

	public function resolve(): ?string
	{
		foreach ($this->serviceNames as $serviceName) {
			/** @var LocaleResolver $resolver */
			$resolver = $this->container->getService($serviceName);
			$locale = $resolver->resolve();

			//TODO - zvalidovat, že je ve whitelistu
			if ($locale !== null) {
				return $locale;
			}
		}

		return null;
	}

}
