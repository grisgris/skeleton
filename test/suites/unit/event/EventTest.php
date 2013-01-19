<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Event;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Event\Event;

/**
 * Test case class for Event.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class EventTest extends TestCase
{
	/**
	 * @var    Event  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Test the constructor.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::__construct
	 */
	public function test__construct()
	{
		$name = 'test';
		$arguments = array(1, 2, 3);

		$event = new Event($name, $arguments);

		$this->assertEquals($name, $event->getName());
		$this->assertEquals($arguments, $event->getArguments());
	}

	/**
	 * Test the getArguments method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::getArguments
	 */
	public function testGetArguments()
	{
		Reflection::setValue($this->_instance, 'args', true);

		$this->assertTrue($this->_instance->getArguments());
	}

	/**
	 * Test the setArguments method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::setArguments
	 */
	public function testSetArguments()
	{
		$arguments = array(
			'foo'   => 'bar',
			'test'  => 'test',
			'test1' => 'test1'
		);

		$this->_instance->setArguments($arguments);

		$this->assertEquals($arguments, $this->_instance->getArguments());
	}

	/**
	 * Test the getArgument method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::getArgument
	 */
	public function testGetArgument()
	{
		$arguments = array('foo' => 'bar', 'test' => 'test');

		Reflection::setValue($this->_instance, 'args', $arguments);

		$this->assertEquals('bar', $this->_instance->getArgument('foo'));
		$this->assertEquals('test', $this->_instance->getArgument('test'));
	}

	/**
	 * Test the getArgument method when the argument doesn't exist.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::getArgument
	 */
	public function testGetArgumentDefault()
	{
		$this->assertNull($this->_instance->getArgument('non-existing'));

		// Specify a default value.
		$this->assertFalse($this->_instance->getArgument('non-existing', false));
	}

	/**
	 * Test the setArgument method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::setArgument
	 */
	public function testSetArgument()
	{
		$this->_instance->setArgument('foo', 'bar');
		$this->_instance->setArgument('test', 'test');

		$this->assertEquals('bar', $this->_instance->getArgument('foo'));
		$this->assertEquals('test', $this->_instance->getArgument('test'));
	}

	/**
	 * Test the setArgument method when reseting an argument.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::setArgument
	 */
	public function testSetArgumentReset()
	{
		// Specify the foo argument.
		$this->_instance->setArgument('foo', 'bar');

		// Reset it with an other value.
		$this->_instance->setArgument('foo', 'test');

		$this->assertEquals('test', $this->_instance->getArgument('foo'));
	}

	/**
	 * Test the count method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::count
	 */
	public function testCount()
	{
		$this->assertCount(0, $this->_instance);

		// Add a few arguments.
		$this->_instance->setArgument('foo', 'bar');
		$this->_instance->setArgument('test', 'test');

		$this->assertCount(2, $this->_instance);
	}

	/**
	 * Test the stopPropagation method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::stopPropagation
	 */
	public function testStopPropagation()
	{
		$this->_instance->stopPropagation();

		$this->assertTrue($this->_instance->isStopped());
	}

	/**
	 * Test the isStopped method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::isStopped
	 */
	public function testIsStopped()
	{
		Reflection::setValue($this->_instance, 'propagate', 'foo');

		$this->assertFalse($this->_instance->isStopped());

		Reflection::setValue($this->_instance, 'propagate', false);

		$this->assertTrue($this->_instance->isStopped());
	}

	/**
	 * Test the getName method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::getName
	 */
	public function testGetName()
	{
		Reflection::setValue($this->_instance, 'name', 'foo');

		$this->assertEquals($this->_instance->getName(), 'foo');
	}

	/**
	 * Test serialize, unserialize.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Event::serialize
	 * @covers  Grisgris\Event\Event::unserialize
	 */
	public function testSerializeUnserialize()
	{
		$arguments = array(
			'foo'   => 'bar',
			'test'  => 'test',
			'test1' => 'test1'
		);

		$event = new Event('test', $arguments);

		$serialized = serialize($event);
		$event2 = unserialize($serialized);

		$this->assertEquals($event, $event2);
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

		$this->_instance = new Event('test');
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
