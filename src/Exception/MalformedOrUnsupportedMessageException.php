<?php declare(strict_types = 1);

namespace Modette\Translation\Exception;

use Modette\Exceptions\LogicalException;

final class MalformedOrUnsupportedMessageException extends LogicalException
{

	/** @var string */
	private $pattern;

	/** @var string */
	private $locale;

	private function __construct(string $message, string $pattern, string $locale)
	{
		parent::__construct($message);
		$this->pattern = $pattern;
		$this->locale = $locale;
	}

	public static function forPattern(string $pattern, string $locale): self
	{
		return new self(
			sprintf(
				'Message pattern "%s" is invalid or not supported.',
				$pattern
			),
			$pattern,
			$locale
		);
	}

	public function getPattern(): string
	{
		return $this->pattern;
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

}
