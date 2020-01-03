<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Formatting;

use Modette\Translation\Formatting\IntlMessageFormatter;
use Modette\Translation\Formatting\MessageFormatterFactory;
use Modette\Translation\Formatting\SymfonyMessageFormatter;
use PHPUnit\Framework\TestCase;

final class MessageFormatterFactoryTest extends TestCase
{

	public function test(): void
	{
		$formatter = MessageFormatterFactory::create();

		self::assertTrue($formatter instanceof IntlMessageFormatter || $formatter instanceof SymfonyMessageFormatter);
	}

}
