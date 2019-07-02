<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Translator;
use Modette\Translation\Utils\LocaleHelper;
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

	public function resolve(Translator $translator): ?string
	{
		foreach ($this->resolverServiceNames as $resolverServiceName) {
			/** @var LocaleResolver $resolver */
			$resolver = $this->container->getService($resolverServiceName);
			$locale = $resolver->resolve($translator);

			if ($locale !== null && LocaleHelper::isWhitelisted($locale, $translator->getLocaleWhiteList())) {
				return $locale;
			}
		}

		return null;
	}

}
