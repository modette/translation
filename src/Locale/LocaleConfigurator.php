<?php declare(strict_types = 1);

namespace Modette\Translation\Locale;

interface LocaleConfigurator
{

	public function configure(string $locale): void;

}
