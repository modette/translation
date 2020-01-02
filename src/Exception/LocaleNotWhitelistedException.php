<?php declare(strict_types = 1);

namespace Modette\Translation\Exception;

use Modette\Exceptions\LogicalException;

final class LocaleNotWhitelistedException extends LogicalException
{

	/** @var string */
	private $locale;

	/** @var string[] */
	private $whitelist;

	/**
	 * @param string[] $whitelist
	 */
	private function __construct(string $message, string $locale, array $whitelist)
	{
		parent::__construct($message);
		$this->locale = $locale;
		$this->whitelist = $whitelist;
	}

	/**
	 * @param string[] $whitelist
	 */
	public static function forWhitelist(string $locale, array $whitelist): self
	{
		return new self(
			sprintf('Locale "%s" is not whitelisted. Whitelisted are: "%s"', $locale, implode(', ', $whitelist)),
			$locale,
			$whitelist
		);
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

	/**
	 * @return string[]
	 */
	public function getWhitelist(): array
	{
		return $this->whitelist;
	}

}
