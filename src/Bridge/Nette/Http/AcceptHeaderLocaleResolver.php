<?php declare(strict_types = 1);

namespace Modette\Translation\Bridge\Nette\Http;

use Modette\Translation\Locale\LocaleResolver;
use Nette\Http\IRequest;
use Nette\Http\Request;

final class AcceptHeaderLocaleResolver implements LocaleResolver
{

	/** @var IRequest */
	private $request;

	public function __construct(IRequest $request)
	{
		$this->request = $request;
	}

	/**
	 * @param string[] $localeWhitelist
	 */
	public function resolve(array $localeWhitelist): ?string
	{
		if ($this->request instanceof Request) {
			return $this->request->detectLanguage($localeWhitelist);
		}

		return $this->detectLanguage($localeWhitelist);
	}

	/**
	 * Parse Accept-Language header and returns preferred language.
	 *
	 * @param string[] $languages supported languages
	 * @see Copy-pasted from Nette\Http\Request https://github.com/nette/http/blob/3f062bdfe8301eb9d16bb17a9c1ea501ffd20cab/src/Http/Request.php#L287-L318
	 * @author David Grudl
	 */
	private function detectLanguage(array $languages): ?string
	{
		$header = $this->request->getHeader('Accept-Language');
		if ($header === null) {
			return null;
		}

		$s = strtolower($header); // case insensitive
		$s = str_replace('_', '-', $s); // cs_CZ means cs-CZ
		rsort($languages); // first more specific
		preg_match_all('#(' . implode('|', $languages) . ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#', $s, $matches);

		if (!$matches[0]) {
			return null;
		}

		$max = 0;
		$lang = null;
		foreach ($matches[1] as $key => $value) {
			$q = $matches[2][$key] === '' ? 1.0 : (float) $matches[2][$key];
			if ($q > $max) {
				$max = $q;
				$lang = $value;
			}
		}

		return $lang;
	}

}
