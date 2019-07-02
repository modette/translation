<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

interface Loader
{

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array;

}
