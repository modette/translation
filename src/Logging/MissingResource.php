<?php declare(strict_types = 1);

namespace Modette\Translation\Logging;

final class MissingResource
{

	/** @var string[] */
	private $locales;

	/** @var string */
	private $message;

	/** @var int */
	private $count;

	public function __construct(string $locale, string $message)
	{
		$this->locales = [$locale];
		$this->message = $message;
		$this->count = 1;
	}

	/**
	 * @return string[]
	 */
	public function getLocales(): array
	{
		return $this->locales;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function incrementCount(string $locale): void
	{
		if (!in_array($locale, $this->locales, true)) {
			$this->locales[] = $locale;
		}

		++$this->count;
	}

	public function getCount(): int
	{
		return $this->count;
	}

}
