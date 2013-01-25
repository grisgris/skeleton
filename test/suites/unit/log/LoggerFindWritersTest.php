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
 * Test class for Logger message routing.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class LoggerFindWritersTest extends TestCase
{
	/**
	 * @var    Logger  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Writer  A mock Writer object for testing.
	 * @since  13.1
	 */
	private $_mock1;

	/**
	 * @var    Writer  A mock Writer object for testing.
	 * @since  13.1
	 */
	private $_mock2;

	/**
	 * @var    Writer  A mock Writer object for testing.
	 * @since  13.1
	 */
	private $_mock3;

	/**
	 * @var    Writer  A mock Writer object for testing.
	 * @since  13.1
	 */
	private $_mock4;

	/**
	 * @var    Writer  A mock Writer object for testing.
	 * @since  13.1
	 */
	private $_mock5;

	/**
	 * Test the `findWriters` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::findWriters
	 * @since   13.1
	 */
	public function testFindWritersWithNoticePriorityInCategoryFoo()
	{
		$writers = Reflection::invoke($this->_instance, 'findWriters', Message::NOTICE, 'foo');

		$this->assertEquals(2, count($writers));
		$this->assertSame(array($this->_mock1, $this->_mock3),	$writers);
	}

	/**
	 * Test the `findWriters` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::findWriters
	 * @since   13.1
	 */
	public function testFindWritersWithErrorPriority()
	{
		$writers = Reflection::invoke($this->_instance, 'findWriters', Message::ERROR, '');

		$this->assertEquals(1, count($writers));
		$this->assertSame(array($this->_mock2),	$writers);
	}

	/**
	 * Test the `findWriters` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::findWriters
	 * @since   13.1
	 */
	public function testFindWritersWithInfoPriority()
	{
		$writers = Reflection::invoke($this->_instance, 'findWriters', Message::INFO, '');

		$this->assertEquals(2, count($writers));
		$this->assertSame(array($this->_mock3, $this->_mock5),	$writers);
	}

	/**
	 * Test the `findWriters` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Log\Logger::findWriters
	 * @since   13.1
	 */
	public function testFindWritersWithInfoPriorityInCategoryFoo()
	{
		$writers = Reflection::invoke($this->_instance, 'findWriters', Message::INFO, 'foo');

		$this->assertEquals(3, count($writers));
		$this->assertSame(array($this->_mock1, $this->_mock3, $this->_mock4),	$writers);
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

		$this->_mock1 = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$this->_mock2 = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$this->_mock3 = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$this->_mock4 = $this->getMockForAbstractClass('Grisgris\Log\Writer');
		$this->_mock5 = $this->getMockForAbstractClass('Grisgris\Log\Writer');

		$this->_instance->registerWriter($this->_mock1, Message::ALL, array('foo'));
		$this->_instance->registerWriter($this->_mock2, Message::ERROR);
		$this->_instance->registerWriter($this->_mock3, Message::ALL & ~Message::ERROR);
		$this->_instance->registerWriter($this->_mock4, Message::INFO, array('foo'));
		$this->_instance->registerWriter($this->_mock5, Message::INFO, array('foo'), true);
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
