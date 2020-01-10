<?php declare(strict_types = 1);

namespace Modette\Translation;

if (!function_exists('Modette\Translation\__')) {

	/**
	 * @param mixed[] $parameters
	 */
	function __(string $message, array $parameters = [], ?string $locale = null): string
	{
		if (!isset($GLOBALS[Translator::class])) {
			$GLOBALS[Translator::class] = TranslatorHolder::getInstance()->getTranslator();
		}

		$translator = $GLOBALS[Translator::class];
		assert($translator instanceof Translator);

		return $translator->translate($message, $parameters, $locale);
	}

}
