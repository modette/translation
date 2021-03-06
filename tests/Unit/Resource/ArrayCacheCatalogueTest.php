<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Resource;

use Modette\Translation\Resource\ArrayCacheCatalogue;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Translation\Fixtures\ArrayCatalogue;

final class ArrayCacheCatalogueTest extends TestCase
{

	public function test(): void
	{
		$arrayCatalogue = new ArrayCatalogue([
			'en' => [],
			'en-US' => ['key1' => 'translation'],
		]);

		$catalogue = new ArrayCacheCatalogue($arrayCatalogue);

		$catalogue->getMessage('key1', 'cs');
		$catalogue->getMessage('key1', 'cs');

		$catalogue->getMessage('key1', 'en');

		$catalogue->getMessage('key1', 'en-US');
		$catalogue->getMessage('key2', 'en-US');
		$catalogue->getMessage('key2', 'en-US');

		self::assertSame(
			[
				'cs' => [
					'key1' => 1,
				],
				'en' => [
					'key1' => 1,
				],
				'en-US' => [
					'key1' => 1,
					'key2' => 1,
				],
			],
			$arrayCatalogue->getCalls()
		);
	}

}
