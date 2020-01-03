<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Fixtures;

use Modette\Translation\Resource\Loader;

final class ArrayLoader implements Loader
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

	/**
	 * @return string[]
	 */
	public function loadAllMessages(string $locale): array
	{
		$this->calls[$locale] = isset($this->calls[$locale])
			? $this->calls[$locale] + 1
			: 1;

		return $this->messages[$locale] ?? [];
	}

	/**
	 * @return int[]
	 */
	public function getCalls(): array
	{
		return $this->calls;
	}

}
