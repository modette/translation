<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Localization;

use Modette\Translation\Translator;
use Nette\Localization\ITranslator;

final class NetteTranslator implements ITranslator
{

	/** @var Translator */
	private $translator;

	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param mixed ...$parameters ['message', 'parameters'], 'locale'
	 */
	public function translate($message, ...$parameters): string
	{
		if (!is_string($message)) {
			return (string) $message;
		}

		$messageParameters = $parameters[0] ?? [];

		if (!is_array($messageParameters)) {
			// nette/forms gives null parameter by default
			if ($messageParameters === null) {
				$messageParameters = [];
			}

			// Count parameter, used in nette/forms
			if (is_int($messageParameters) || is_float($messageParameters)) {
				//TODO - maybe sprintf %s support?? - get catalogue seems as easiest way
				$messageParameters = ['count' => $messageParameters];
			}

			throw new InvalidArgumentException('Unsupported type of parameter given.');
		}

		$locale = $parameters[1] ?? null;

		return $this->translator->translate($message, $messageParameters, $locale);
	}

}
