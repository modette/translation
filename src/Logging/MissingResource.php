<?php declare(strict_types = 1);

namespace Modette\Translation\Logging;

final class MissingResource
{

	/** @var string */
	private $locale;

	/** @var string */
	private $message;

	public function __construct(string $locale, string $message)
	{
		$this->locale = $locale;
		$this->message = $message;
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

}
