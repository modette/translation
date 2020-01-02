<?php declare(strict_types = 1);

namespace Modette\Translation\Exception;

use Modette\Exceptions\LogicalException;

final class MalformedLocaleException extends LogicalException
{

	/** @var string */
	private $locale;

	private function __construct(string $message, string $locale)
	{
		parent::__construct($message);
		$this->locale = $locale;
	}

	public static function forUnknownFormat(string $locale): self
	{
		return new self(
			sprintf('Invalid "%s" locale.', $locale),
			$locale
		);
	}

	public static function forNonNormalizedFormat(string $locale, string $normalized): self
	{
		return new self(
			sprintf('Invalid "%s" locale, use "%s" format instead.', $locale, $normalized),
			$locale
		);
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

}
