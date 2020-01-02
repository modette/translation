<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Router;

use Modette\Translation\Locale\LocaleResolver;
use Nette\Http\IRequest;
use Nette\Routing\Router;

final class RouterLocaleResolver implements LocaleResolver
{

	/** @var IRequest */
	private $request;

	/** @var Router */
	private $router;

	/** @var string */
	private $parameterName = 'locale';

	public function __construct(IRequest $request, Router $router)
	{
		$this->request = $request;
		$this->router = $router;
	}

	public function setParameterName(string $parameterName): void
	{
		$this->parameterName = $parameterName;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		$match = $this->router->match($this->request);

		if ($match !== null && array_key_exists($this->parameterName, $match)) {
			return $match[$this->parameterName];
		}

		return null;
	}

}
