<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\DI;

use Modette\Translation\Resource\Loader;

final class LazyMultiLoader implements Loader
{

	/**
	 * @inheritDoc
	 */
	public function loadAllMessages(string $locale): array
	{
		// TODO: Implement loadAllMessages() method.
	}

}
