<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

final class MultiLocaleResolver implements LocaleResolver
{

	/** @var LocaleResolver[] */
	private $resolvers;

	/**
	 * @param LocaleResolver[] $resolvers
	 */
	public function __construct(array $resolvers)
	{
		$this->resolvers = $resolvers;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		foreach ($this->resolvers as $resolver) {
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
