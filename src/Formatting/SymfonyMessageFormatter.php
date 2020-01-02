<?php declare(strict_types = 1);

namespace Modette\Translation\Formatting;

use Modette\Translation\Exception\MalformedOrUnsupportedMessageException;
use Symfony\Polyfill\Intl\MessageFormatter\MessageFormatter as OriginalSymfonyMessageFormatter;

final class SymfonyMessageFormatter implements MessageFormatter
{

	/**
	 * @param mixed[] $parameters
	 * @throws MalformedOrUnsupportedMessageException
	 */
	public function formatMessage(string $locale, string $pattern, array $parameters): string
	{
		$message = OriginalSymfonyMessageFormatter::formatMessage($locale, $pattern, $parameters);

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
		$formatter = OriginalSymfonyMessageFormatter::create($locale, $pattern);

		if ($formatter === null) {
			throw MalformedOrUnsupportedMessageException::forPattern($pattern, $locale);
		}
	}

}
