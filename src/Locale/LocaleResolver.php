<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

interface LocaleResolver
{

	public function resolve(): ?string;

}
