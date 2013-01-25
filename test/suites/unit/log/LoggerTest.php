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

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Log\Message;
use Grisgris\Log\Logger;

/**
 * Test class for Logger.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class LoggerTest extends TestCase
{
	/**
	 * @var    Logger  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Test the `registerWriter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::registerWriter
	 * @since   13.1
	 */
	public function testRegisterWriter()
	{
		$mock = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$hash = spl_object_hash($mock);
		$this->_instance->registerWriter($mock, Message::CRITICAL, array('Foobar'));

		$writers = Reflection::getValue($this->_instance, 'writers');
		$this->assertEquals(1, count($writers));
		$this->assertEquals(Message::CRITICAL, $writers[$hash]->priorities);
		$this->assertEquals(array('foobar'), $writers[$hash]->categories);
		$this->assertSame($mock, $writers[$hash]->writer);
	}

	/**
	 * Test the `unregisterWriter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::unregisterWriter
	 * @since   13.1
	 */
	public function testUnregisterWriter()
	{
		$mock = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$hash = spl_object_hash($mock);
		$this->_instance->registerWriter($mock);

		$writers = Reflection::getValue($this->_instance, 'writers');
		$this->assertEquals(1, count($writers));
		$this->assertSame($mock, $writers[$hash]->writer);

		$this->_instance->unregisterWriter($mock);

		$writers = Reflection::getValue($this->_instance, 'writers');
		$this->assertEmpty($writers);
	}

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->_instance = new Logger;
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function tearDown()
	{
		$this->_instance = null;

		parent::tearDown();
	}
}
