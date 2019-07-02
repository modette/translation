<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

interface Catalogue
{

	/**
	 * @param string[] $localeList
	 */
	public function getMessage(string $message, array $localeList): ?string;

}
