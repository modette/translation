<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit;

use Modette\Translation\DefaultTranslator;
use Modette\Translation\Formatting\IntlMessageFormatter;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\Translator;
use Modette\Translation\TranslatorHolder;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\ArrayCatalogue;
use Tests\Modette\Translation\Fixtures\FakeLocaleResolver;
use function Modette\Translation\__;

/**
 * @runTestsInSeparateProcesses
 */
final class ShortcutTest extends TestCase
{

	public function test(): void
	{
		TranslatorHolder::setTranslator(DefaultTranslator::fromRawLocales('en', ['cs'], [], new FakeLocaleResolver(), new ArrayCatalogue([
			'en' => [
				'no-param' => 'No parameters',
				'apples' => 'I have {apples} apples.',
			],
			'cs' => [
				'apples' => 'Já mám {apples} jablka.',
			],
		]), new IntlMessageFormatter(), new TranslationsLogger()));

		self::assertTrue(function_exists('Modette\Translation\__'));

		self::assertFalse(isset($GLOBALS[Translator::class]));
		self::assertSame('I have 3 apples.', __('apples', ['apples' => 3]));
		self::assertTrue(isset($GLOBALS[Translator::class]));

		self::assertSame('No parameters', __('no-param'));
		self::assertSame('I have 3 apples.', __('apples', ['apples' => 3]));
		self::assertSame('Já mám 3 jablka.', __('apples', ['apples' => 3], 'cs'));
	}

}
