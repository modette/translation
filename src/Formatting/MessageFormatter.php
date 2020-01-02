<?php declare(strict_types = 1);

namespace Modette\Translation\Formatting;

use Modette\Translation\Exception\MalformedOrUnsupportedMessageException;

interface MessageFormatter
{

	/**
	 * @param mixed[] $parameters
	 * @throws MalformedOrUnsupportedMessageException
	 */
	public function formatMessage(string $locale, string $pattern, array $parameters): string;

	/**
	 * @throws MalformedOrUnsupportedMessageException
	 */
	public function validatePattern(string $locale, string $pattern): void;

}
