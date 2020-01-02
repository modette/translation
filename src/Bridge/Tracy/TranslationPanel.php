<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Tracy;

use Modette\Translation\Logging\MissingResource;
use Modette\Translation\Logging\TranslationsLogger;
use Modette\Translation\Translator;
use Tracy\IBarPanel;

final class TranslationPanel implements IBarPanel
{

	/** @var Translator */
	private $translator;

	/** @var MissingResource[] */
	private $missingResources;

	public function __construct(Translator $translator, TranslationsLogger $logger)
	{
		$this->translator = $translator;
		$this->missingResources = $logger->getMissingResources();
	}

	public function getTab(): string
	{
		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return ob_get_clean();
	}

	public function getPanel(): string
	{
		$currentLocale = $this->translator->getCurrentLocale();
		$defaultLocale = $this->translator->getDefaultLocale();
		$localeWhitelist = $this->translator->getLocaleWhitelist();
		$missingResources = $this->missingResources;

		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return ob_get_clean();
	}

}
