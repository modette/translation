<?php declare(strict_types = 1);

namespace Modette\Translation\Logging;

use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class TranslationsLogger
{

	/** @var LoggerInterface|null */
	private $logger;

	/** @var MissingResource[] */
	private $missingResources = [];

	public function __construct(?LoggerInterface $logger = null)
	{
		$this->logger = $logger;
	}

	public function addMissingResource(string $locale, string $message): void
	{
		$this->missingResources[] = new MissingResource($locale, $message);

		if ($this->logger === null) {
			return;
		}

		$this->logger->error(
			sprintf('Missing translation of "%s" for locale "%s"', $message, $locale),
			[
				'locale' => $locale,
				'message' => $message,
			]
		);
	}

	/**
	 * @return MissingResource[]
	 */
	public function getMissingResources(): array
	{
		return $this->missingResources;
	}

}
