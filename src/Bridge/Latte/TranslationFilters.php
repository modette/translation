<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Latte;

use Latte\Runtime\FilterInfo;
use Nette\Localization\ITranslator;

final class TranslationFilters
{

	/** @var ITranslator */
	private $translator;

	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	public function translate(FilterInfo $filterInfo, $message, array $parameters = [], ?string $locale = null): string
	{
		return $this->translator->translate($message, $parameters, $locale);
	}

}
