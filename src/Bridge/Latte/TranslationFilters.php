<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Latte;

use Latte\Runtime\FilterInfo;
use Modette\Translation\Translator;

final class TranslationFilters
{

	/** @var Translator */
	private $translator;

	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	public function translate(FilterInfo $filterInfo, $message, array $parameters = [], ?string $locale = null): string
	{
		return $this->translator->translate($message, $parameters, $locale);
	}

}
