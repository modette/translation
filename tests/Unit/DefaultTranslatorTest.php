<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit;

use Modette\Translation\Bridge\Nette\DI\LazyMultiLocaleResolver;
use Modette\Translation\DefaultTranslator;
use Modette\Translation\Formatting\IntlMessageFormatter;
use Modette\Translation\Logging\TranslationsLogger;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\ArrayCatalogue;

final class DefaultTranslatorTest extends TestCase
{

	public function test(): void
	{
		$translator = DefaultTranslator::fromRawLocales(
			'en',
			['cs', 'de'],
			[],
			new LazyMultiLocaleResolver([], new Container()),
			new ArrayCatalogue([
				'en' => [
					'apples' => 'I have {apples, number, integer} apples.',
				],
			]),
			new IntlMessageFormatter(),
			new TranslationsLogger()
		);

		//TODO - MultiLocaleResolver
		//		- resolver pro testy
		//TODO - měly by být default a fallback jazyky whitelisted?
		//TODO - otestovat
		//		- vytváření translatoru
		//		- validaci jazyků
		//		- jazyk pro konkrétní zprávu
		//		- fallbacky
		//		- více zavolání setCurrentLocale
		//		- volání catalogue se všemi possibleLocales
		self::assertSame('en', $translator->getCurrentLocale());
		self::assertSame('en', $translator->getDefaultLocale());
		self::assertSame(['cs', 'de'], $translator->getLocaleWhitelist());

		self::assertSame('I have 5 apples.', $translator->translate('apples', ['apples' => 5]));
	}

}
