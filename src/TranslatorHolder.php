<?php declare(strict_types = 1);

namespace Modette\Translation;

final class TranslatorHolder
{

	/** @var Translator|null */
	private static $translator;

	/** @var static|null */
	private static $instSelf;

	public static function setTranslator(Translator $translator): void
	{
		self::$translator = $translator;
	}

	/**
	 * @return static
	 */
	public static function getInstance(): self
	{
		if (static::$instSelf === null) {
			if (static::$translator === null) {
				throw new InvalidStateException(sprintf('Call %s::setTranslator() to use %s::getInstance()', static::class, static::class));
			}
			static::$instSelf = new static();
		}
		return static::$instSelf;
	}

	public function getTranslator(): Translator
	{
		return self::$translator;
	}

}
