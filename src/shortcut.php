<?php declare(strict_types = 1);

use Modette\Translation\Translator;
use Modette\Translation\TranslatorHolder;

/** @var Translator|null $translator */
$translator = null;

function _(string $message, array $parameters, ?string $locale = null) use ($translator): string
{
	if ($translator = null) {
		$translator = TranslatorHolder::getInstance()->getTranslator();
	}

	return $translator->translate($message, $parameters, $locale);
}
