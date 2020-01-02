<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Locale\LocaleHelper;
use Modette\Translation\Locale\LocaleResolver;
use Nette\DI\Container;

final class LazyMultiLocaleResolver implements LocaleResolver
{

	/** @var Container */
	private $container;

	/** @var string[] */
	private $resolverServiceNames;

	/**
	 * @param string[] $resolverServiceNames
	 */
	public function __construct(Container $container, array $resolverServiceNames)
	{
		$this->container = $container;
		$this->resolverServiceNames = $resolverServiceNames;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		foreach ($this->resolverServiceNames as $resolverServiceName) {
			$resolver = $this->container->getService($resolverServiceName);
			assert($resolver instanceof LocaleResolver);
			$locale = $resolver->resolve($localeWhitelist);

			if ($locale !== null) {
				$locale = LocaleHelper::normalize($locale);

				if (LocaleHelper::isWhitelisted($locale, $localeWhitelist)) {
					return $locale;
				}
			}
		}

		return null;
	}

}
