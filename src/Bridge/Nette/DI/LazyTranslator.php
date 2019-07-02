<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Translator;
use Nette\DI\Container;

final class LazyTranslator implements Translator
{

	/** @var Container */
	private $container;

	/** @var string */
	private $translatorServiceName;

	/** @var Translator|null */
	private $translator;

	public function __construct(Container $container, string $translatorServiceName)
	{
		$this->container = $container;
		$this->translatorServiceName = $translatorServiceName;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function translate(string $message, array $parameters = [], ?string $locale = null): string
	{
		return $this->getTranslator()->translate($message, $parameters, $locale);
	}

	private function getTranslator(): Translator
	{
		if ($this->translator = null) {
			$translator = $this->container->getService($this->translatorServiceName);
			assert($translator instanceof Translator);
			$this->translator = $translator;
		}

		return $this->translator;
	}

}
