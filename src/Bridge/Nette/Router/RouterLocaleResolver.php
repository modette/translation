<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Router;

use Modette\Translation\Locale\LocaleResolver;
use Modette\Translation\Translator;
use Nette\Http\IRequest;
use Nette\Routing\Router;

final class RouterLocaleResolver implements LocaleResolver
{

	/** @var IRequest */
	private $request;

	/** @var Router */
	private $router;

	/** @var string */
	private $parameter = 'locale';

	public function __construct(IRequest $request, Router $router)
	{
		$this->request = $request;
		$this->router = $router;
	}

	public function setParameterName(string $parameter): void
	{
		$this->parameter = $parameter;
	}

	public function resolve(Translator $translator): ?string
	{
		$match = $this->router->match($this->request);

		if ($match !== null && array_key_exists($this->parameter, $match)) {
			return $match[$this->parameter];
		}

		return null;
	}

}
