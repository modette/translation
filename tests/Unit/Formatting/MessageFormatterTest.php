<?php declare(strict_types = 1);

namespace Tests\Modette\Translation\Unit\Formatting;

use Modette\Translation\Formatting\IntlMessageFormatter;
use Modette\Translation\Formatting\SymfonyMessageFormatter;
use PHPUnit\Framework\TestCase;

final class MessageFormatterTest extends TestCase
{

	/** @var IntlMessageFormatter */
	private static $intlFormatter;

	/** @var SymfonyMessageFormatter */
	private static $symfonyFormatter;

	public static function setUpBeforeClass(): void
	{
		self::$intlFormatter = new IntlMessageFormatter();
		self::$symfonyFormatter = new SymfonyMessageFormatter();
	}

	/**
	 * @param mixed[] $parameters
	 * @dataProvider providerChoice
	 * @dataProvider providerComplex
	 * @dataProvider providerDuration
	 * @dataProvider providerDate
	 * @dataProvider providerNumber
	 * @dataProvider providerSelect
	 * @dataProvider providerPlural
	 * @dataProvider providerSpellout
	 */
	public function testIntl(string $locale, string $message, array $parameters, string $expected): void
	{
		self::assertSame($expected, self::$intlFormatter->formatMessage($locale, $message, $parameters));
		self::$intlFormatter->validatePattern($locale, $message);
	}

	/**
	 * Not supported:
	 * 	- choice
	 * 	- duration
	 * 	- date
	 *  - spellout
	 *
	 * @param mixed[] $parameters
	 * @dataProvider providerComplex
	 * @dataProvider providerNumber
	 * @dataProvider providerSelect
	 * @dataProvider providerPlural
	 */
	public function testSymfony(string $locale, string $message, array $parameters, string $expected): void
	{
		self::assertSame($expected, self::$symfonyFormatter->formatMessage($locale, $message, $parameters));
		self::$intlFormatter->validatePattern($locale, $message);
	}

	/**
	 * @return mixed[]
	 */
	public function providerNumber(): array
	{
		return [
			['en-US', 'I have {0} apples.', [3], 'I have 3 apples.'],
			['en-US', 'I have {0, number, integer} apples.', [3], 'I have 3 apples.'],
			['en_US', 'I have {number_apples, number, integer} apples.', ['number_apples' => 3], 'I have 3 apples.'],
			// TODO - another languages
			//['ar', 'I have {number_apples, number, integer} apples.', ['number_apples' => 3], 'I have ٣ apples.'],
			//['br', 'I have {number_apples, number, integer} apples.', ['number_apples' => 3], 'I have ৩ apples.'],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerDate(): array
	{
		return [
			['en_US', 'Today is {0, date, full} - {0, time}', [1577531843], 'Today is Saturday, December 28, 2019 - 11:17:23 AM'],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerDuration(): array
	{
		return [
			['en-US', 'duration: {0, duration}', [1577531843], 'duration: 438,203:17:23'],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerSpellout(): array
	{
		return [
			['en-US', 'I have {0, spellout} apples', [34], 'I have thirty-four apples'],
			['ar', 'لدي {0, spellout} تفاحة', [34], 'لدي أربعة و ثلاثون تفاحة'],
		];
	}

	/**
	 * {data, plural, offsetValue =value{message}... other{message}}
	 *
	 * @return mixed[]
	 */
	public function providerPlural(): array
	{
		return [
			['en-US', 'I have {number_apples, plural, =0{no apples} =1{one apple} other{# apples}}', ['number_apples' => 0], 'I have no apples'],
			['en-US', 'I have {number_apples, plural, =0{no apples} =1{one apple} other{# apples}}', ['number_apples' => 1], 'I have one apple'],
			['en-US', 'I have {number_apples, plural, =0{no apples} =1{one apple} other{# apples}}', ['number_apples' => 10], 'I have 10 apples'],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerChoice(): array
	{
		return [
			[
				'en-US',
				'The value of {0,number} is {0, choice,
				 	0 #between 0 and 19|
				 	20 #between 20 and 39|
				 	40 #between 40 and 59|
				 	60 #between 60 and 79|
				 	80 #between 80 and 100|
				 	100 <more than 100}',
				[60],
				'The value of 60 is between 60 and 79',
			],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerSelect(): array
	{
		return [
			[
				'en-US',
				'{gender, select, female {She has some apples} male {He has some apples.}other {It has some apples.}}',
				['gender' => 'female'],
				'She has some apples',
			],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function providerComplex(): array
	{
		$complexPattern
			= '{gender_of_host, select, ' .
				'female {' .
					'{num_guests, plural, offset:1 ' .
						'=0 {{host} does not give a party.}' .
						'=1 {{host} invites {guest} to her party.}' .
						'=2 {{host} invites {guest} and one other person to her party.}' .
						'other {{host} invites {guest} and # other people to her party.}}}' .
				'male {' .
					'{num_guests, plural, offset:1 ' .
						'=0 {{host} does not give a party.}' .
						'=1 {{host} invites {guest} to his party.}' .
						'=2 {{host} invites {guest} and one other person to his party.}' .
						'other {{host} invites {guest} and # other people to his party.}}}' .
				'other {' .
					'{num_guests, plural, offset:1 ' .
						'=0 {{host} does not give a party.}' .
						'=1 {{host} invites {guest} to their party.}' .
						'=2 {{host} invites {guest} and one other person to their party.}' .
						'other {{host} invites {guest} and # other people to their party.}}}}';

		return [
			[
				'en-US',
				$complexPattern,
				['gender_of_host' => 'female', 'num_guests' => 5, 'host' => 'Hanae', 'guest' => 'Younes'],
				'Hanae invites Younes and 4 other people to her party.',
			],

			[
				'en-US',
				$complexPattern,
				['gender_of_host' => 'female', 'num_guests' => 0, 'host' => 'Hanae', 'guest' => 'Younes'],
				'Hanae does not give a party.',
			],

			[
				'en-US',
				$complexPattern,
				['gender_of_host' => 'other', 'num_guests' => 1, 'host' => 'Hanae and John', 'guest' => 'Younes'],
				'Hanae and John invites Younes to their party.',
			],

			[
				'en-US',
				$complexPattern,
				['gender_of_host' => 'male', 'num_guests' => 2, 'host' => 'John', 'guest' => 'Mr. Bean'],
				'John invites Mr. Bean and one other person to his party.',
			],
		];
	}

}
