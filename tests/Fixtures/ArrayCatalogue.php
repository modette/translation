<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Fixtures;

use Modette\Translation\Resource\Catalogue;

final class ArrayCatalogue implements Catalogue
{

	/** @var string[][] */
	private $messages;

	/** @var int[] */
	private $calls = [];

	/**
	 * @param string[][] $messages
	 */
	public function __construct(array $messages)
	{
		$this->messages = $messages;
	}

	public function getMessage(string $message, string $locale): ?string
	{
		$this->calls[$locale][$message] = isset($this->calls[$locale][$message])
			? $this->calls[$locale][$message] + 1
			: 1;

		return $this->messages[$locale][$message] ?? null;
	}

	/**
	 * @return int[]
	 */
	public function getCalls(): array
	{
		return $this->calls;
	}

}
