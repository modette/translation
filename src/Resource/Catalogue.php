<?php declare(strict_types = 1);

namespace Modette\Translation\Resource;

interface Catalogue
{

	public function getMessage(string $message, string $locale): ?string;

}
