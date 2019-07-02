<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Tracy;

use Tracy\IBarPanel;

/**
 * @todo https://github.com/contributte/translation/blob/master/src/Tracy/Panel.php
 */
final class TranslationPanel implements IBarPanel
{

	public function getTab(): ?string
	{
		return null;
	}

	public function getPanel(): ?string
	{
		return null;
	}

}
