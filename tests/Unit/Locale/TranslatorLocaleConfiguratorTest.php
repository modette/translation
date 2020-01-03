<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Locale;

use Modette\Translation\Locale\TranslatorLocaleConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\FakeLocaleConfigurator;
use Tests\Modette\Translation\Fixtures\FakeTranslator;

final class TranslatorLocaleConfiguratorTest extends TestCase
{

	public function test(): void
	{
		$translator = new FakeTranslator('en');
		$configurator = new FakeLocaleConfigurator();
		$mainConfigurator = new TranslatorLocaleConfigurator($translator, $configurator);

		$mainConfigurator->configure('cs');

		self::assertSame('cs', $configurator->getLocale());
		self::assertSame('cs', $translator->getCurrentLocale());
	}

}
