<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Localization;

use Modette\Exceptions\Logic\InvalidArgumentException;
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
	 * @param mixed $message
	 * @param mixed ...$parameters ['parameters', 'array'], 'locale'
	 */
	public function translate($message, ...$parameters): string
	{
		if (!is_string($message)) {
			return (string) $message;
		}

		$messageParameters = $parameters[0] ?? [];

		if (!is_array($messageParameters)) {
			if ($messageParameters === null) {
				// nette/forms gives null parameter by default
				$messageParameters = [];
			} elseif (is_int($messageParameters) || is_float($messageParameters)) {
				// Count parameter, used in nette/forms
				$messageParameters = ['count' => $messageParameters];
			} else {
				throw new InvalidArgumentException('Unsupported type of parameter given.');
			}
		}

		$locale = $parameters[1] ?? null;

		return $this->translator->translate($message, $messageParameters, $locale);
	}

}
