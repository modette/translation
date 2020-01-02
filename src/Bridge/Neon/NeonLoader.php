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
		// todo - načíst překlady
		//		- klíče musí být string
		//		- překlady musí odpovídat patternu pro daný jazyk MessageFormatter:validatePattern
		return [];
	}

}
