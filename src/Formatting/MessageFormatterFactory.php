<?php declare(strict_types = 1);

namespace Modette\Translation\Formatting;

use MessageFormatter as OriginalIntlMessageFormatter;
use Modette\Exceptions\Logic\InvalidStateException;
use Symfony\Polyfill\Intl\MessageFormatter\MessageFormatter as OriginalSymfonyMessageFormatter;

final class MessageFormatterFactory
{

	public static function create(): MessageFormatter
	{
		if (class_exists(OriginalIntlMessageFormatter::class)) {
			return new IntlMessageFormatter();
		}

		if (class_exists(OriginalSymfonyMessageFormatter::class)) {
			return new SymfonyMessageFormatter();
		}

		throw new InvalidStateException(
			sprintf(
				'Cannot find compatible "%s", please install "ext-intl" or "symfony/polyfill-intl-messageformatter" or create your own.',
				MessageFormatter::class
			)
		);
	}

}
