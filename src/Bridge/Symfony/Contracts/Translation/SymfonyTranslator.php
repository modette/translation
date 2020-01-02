<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Symfony\Contracts\Translation;

use Modette\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SymfonyTranslator implements TranslatorInterface
{

	/** @var Translator */
	private $translator;

	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function trans(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): string
	{
		if ($domain !== null) {
			$message = sprintf('%s.%s', $domain, $message);
		}

		return $this->translator->translate($message, $parameters, $locale);
	}

}
