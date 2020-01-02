<?php declare(strict_types = 1);

namespace Modette\Translation\Formatting;

use MessageFormatter as OriginalIntlMessageFormatter;
use Modette\Translation\Exception\MalformedOrUnsupportedMessageException;

final class IntlMessageFormatter implements MessageFormatter
{

	/**
	 * @param mixed[] $parameters
	 * @throws MalformedOrUnsupportedMessageException
	 */
	public function formatMessage(string $locale, string $pattern, array $parameters): string
	{
		$message = OriginalIntlMessageFormatter::formatMessage($locale, $pattern, $parameters);

		if (!is_string($message)) {
			throw MalformedOrUnsupportedMessageException::forPattern($pattern, $locale);
		}

		return $message;
	}

	/**
	 * @throws MalformedOrUnsupportedMessageException
	 */
	public function validatePattern(string $locale, string $pattern): void
	{
		$formatter = OriginalIntlMessageFormatter::create($locale, $pattern);

		if ($formatter === null) {
			throw MalformedOrUnsupportedMessageException::forPattern($pattern, $locale);
		}
	}

}
