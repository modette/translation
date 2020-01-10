<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit;

use Modette\Exceptions\Logic\InvalidStateException;
use Modette\Translation\DefaultTranslator;
use Modette\Translation\Formatting\IntlMessageFormatter;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\TranslatorHolder;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\ArrayCatalogue;
use Tests\Modette\Translation\Fixtures\FakeLocaleResolver;

/**
 * @runTestsInSeparateProcesses
 */
final class TranslatorHolderTest extends TestCase
{

	public function testOk(): void
	{
		$translator = DefaultTranslator::fromRawLocales('en', [], [], new FakeLocaleResolver(), new ArrayCatalogue([]), new IntlMessageFormatter(), new TranslationsLogger());
		TranslatorHolder::setTranslator($translator);

		self::assertInstanceOf(DefaultTranslator::class, TranslatorHolder::getInstance()->getTranslator());
	}

	public function testNotConfigured(): void
	{
		$this->expectException(InvalidStateException::class);
		$this->expectExceptionMessage('Call Modette\Translation\TranslatorHolder::setTranslator() to use Modette\Translation\TranslatorHolder::getInstance()');

		TranslatorHolder::getInstance()->getTranslator();
	}

}
