<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

use Modette\Translation\Translator;

interface LocaleResolver
{

	public function resolve(Translator $translator): ?string;

}
