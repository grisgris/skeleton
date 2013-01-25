<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Log;

use Grisgris\Test\TestCase;

use Grisgris\Date\Date;
use Grisgris\Log\Message;

/**
 * Test class for Message.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class MessageTest extends TestCase
{
	/**
	 * Verify the default values for the log message object.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Message::__construct
	 * @since   13.1
	 */
	public function testDefaultValues()
	{
		$tmp = new Message('Lorem ipsum dolor sit amet');
		$date = new Date;

		// Message.
		$this->assertEquals('Lorem ipsum dolor sit amet', $tmp->message);

		// Priority.
		$this->assertEquals(Message::INFO, $tmp->priority);

		// Category.
		$this->assertEmpty($tmp->category);

		// Date.
		$this->assertEquals($date->toISO8601(), $tmp->date->toISO8601());
	}

	/**
	 * Verify the priority for the entry object cannot be something not in the approved list.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Message::__construct
	 * @since   13.1
	 */
	public function testBadPriorityValues()
	{
		$tmp = new Message('Lorem ipsum dolor sit amet', Message::ALL);
		$this->assertEquals(Message::INFO, $tmp->priority);

		$tmp = new Message('Lorem ipsum dolor sit amet', 23642872);
		$this->assertEquals(Message::INFO, $tmp->priority);

		$tmp = new Message('Lorem ipsum dolor sit amet', 'foobar');
		$this->assertEquals(Message::INFO, $tmp->priority);
	}

	/**
	 * Test that non-standard category values are sanitized.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Message::__construct
	 * @since   13.1
	 */
	public function testCategorySanitization()
	{
		// Category should always be lowercase.
		$tmp = new Message('Lorem ipsum dolor sit amet', Message::INFO, 'TestingTheCategory');
		$this->assertEquals('testingthecategory', $tmp->category);

		// Category should not have spaces.
		$tmp = new Message('Lorem ipsum dolor sit amet', Message::INFO, 'testing the category');
		$this->assertEquals('testingthecategory', $tmp->category);

		// Category should not have special characters.
		$tmp = new Message('Lorem ipsum dolor sit amet', Message::INFO, 'testing@#$^the*&@^#*&category');
		$this->assertEquals('testingthecategory', $tmp->category);

		// Category should allow numbers.
		$tmp = new Message('Lorem ipsum dolor sit amet', Message::INFO, 'testing1the2category');
		$this->assertEquals('testing1the2category', $tmp->category);
	}
}
