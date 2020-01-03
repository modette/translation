<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Locale;

use Modette\Translation\Locale\MultiLocaleConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\FakeLocaleConfigurator;

final class MultiLocaleConfiguratorTest extends TestCase
{

	public function test(): void
	{
		/** @var FakeLocaleConfigurator[] $configurators */
		$configurators = [
			new FakeLocaleConfigurator(),
			new FakeLocaleConfigurator(),
			new FakeLocaleConfigurator(),
		];

		$mainConfigurator = new MultiLocaleConfigurator($configurators);
		$mainConfigurator->configure('en');

		foreach ($configurators as $configurator) {
			self::assertSame('en', $configurator->getLocale());
		}
	}

}
