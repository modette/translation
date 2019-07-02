<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Neon;

use Modette\Translation\Resource\Loader;

class NeonLoader implements Loader
{

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array
	{
		// TODO: Implement loadAllMessages() method.
		//  - validovat, že všechny překlady jsou string
		return [];
	}

}
