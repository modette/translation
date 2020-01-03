<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Resource;

use Modette\Translation\Resource\ArrayCacheLoader;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\ArrayLoader;

final class ArrayCacheLoaderTest extends TestCase
{

	public function test(): void
	{
		$arrayLoader = new ArrayLoader([
			'en' => [],
			'en-US' => ['key' => 'translation'],
		]);

		$loader = new ArrayCacheLoader($arrayLoader);
		$loader->loadAllMessages('cs');
		$loader->loadAllMessages('cs');

		$loader->loadAllMessages('en');
		$loader->loadAllMessages('en');
		$loader->loadAllMessages('en');

		$loader->loadAllMessages('en-US');
		$loader->loadAllMessages('en-US');

		$loader->loadAllMessages('en-GB');

		self::assertSame(
			[
				'cs' => 1,
				'en' => 1,
				'en-US' => 1,
				'en-GB' => 1,
			],
			$arrayLoader->getCalls()
		);
	}

}
