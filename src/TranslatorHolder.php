<?php declare(strict_types = 1);

namespace Modette\Translation;

use Modette\Exceptions\Logic\InvalidStateException;

final class TranslatorHolder
{

	/** @var Translator|null */
	private static $translator;

	/** @var static|null */
	private static $instSelf;

	/** @var Translator */
	private $instTranslator;

	public static function setTranslator(Translator $translator): void
	{
		self::$translator = $translator;
	}

	public static function getInstance(): self
	{
		if (self::$instSelf === null) {
			if (self::$translator === null) {
				throw new InvalidStateException(sprintf('Call %s::setTranslator() to use %s::getInstance()', self::class, self::class));
			}

			self::$instSelf = new self(self::$translator);
		}

		return self::$instSelf;
	}

	private function __construct(Translator $translator)
	{
		$this->instTranslator = $translator;
	}

	public function getTranslator(): Translator
	{
		return $this->instTranslator;
	}

}
