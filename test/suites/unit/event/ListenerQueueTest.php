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

use Grisgris\Event\ListenerQueue;

use stdClass;

/**
 * Test case class for ListenerQueue.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class ListenerQueueTest extends TestCase
{
	/**
	 * @var    Event  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Test the constructor.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::__construct
	 */
	public function test__construct()
	{
		$this->assertInstanceOf('SplPriorityQueue', Reflection::getValue($this->_instance, 'queue'));
		$this->assertInstanceOf('SplObjectStorage', Reflection::getValue($this->_instance, 'storage'));
	}

	/**
	 * Test the attach method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::attach
	 */
	public function testAttach()
	{
		// Attach a listener with a priority 1.
		$listener = new stdClass;
		$this->_instance->attach($listener, 1);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		// Assert it has been attached.
		$this->assertContains($listener, $listeners);

		// Attach a second listener.
		$listener2 = function() {};
		$this->_instance->attach($listener2, 1);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		// Assert it has been attached.
		$this->assertContains($listener2, $listeners);
	}

	/**
	 * Test the attach method with an already attached object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::attach
	 */
	public function testAttachExisting()
	{
		// Attach a listener with a priority 1.
		$listener = new stdClass;
		$this->_instance->attach($listener, 1);

		// Try attaching it twice.
		$this->_instance->attach($listener, 2);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		// Assert it hasn't been attached twice.
		$this->assertCount(1, $listeners);
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the attach method with an already attached closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::attach
	 */
	public function testAttachExistingClosure()
	{
		// Attach a listener with a priority 1.
		$listener = function() {};
		$this->_instance->attach($listener, 1);

		// Try attaching it twice.
		$this->_instance->attach($listener, 2);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		$this->assertCount(1, $listeners);
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the detach method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::detach
	 */
	public function testDetach()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->_instance->attach($listener, 1);

		// Detach it.
		$this->_instance->detach($listener);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		$this->assertEmpty($listeners);
	}

	/**
	 * Test the detach method with a closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::detach
	 */
	public function testDetachClosure()
	{
		// Attach a listener.
		$listener = function() {};
		$this->_instance->attach($listener, -12);

		// Detach it.
		$this->_instance->detach($listener);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		$this->assertEmpty($listeners);
	}

	/**
	 * Test detaching a non attached object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::detach
	 */
	public function testDetachNonExisting()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->_instance->attach($listener, 1);

		// Detach a non attached one.
		$listener1 = new stdClass;
		$this->_instance->detach($listener1);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		// Assert the first listener is still here.
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the contains method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::contains
	 */
	public function testContains()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->_instance->attach($listener);

		// Attach a second one
		$listener2 = function() {};
		$this->_instance->attach($listener2);

		$this->assertTrue($this->_instance->contains($listener));
		$this->assertTrue($this->_instance->contains($listener2));
	}

	/**
	 * Test the contains method without any attached listener.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::contains
	 */
	public function testContainsNonExisting()
	{
		$this->assertFalse($this->_instance->contains(new stdClass));
	}

	/**
	 * Test the getPriority method
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getPriority
	 */
	public function testGetPriority()
	{
		// Attach a listener with a priority = -52.
		$listener = new stdClass;
		$this->_instance->attach($listener, -52);

		// Attach a second one with a priority = 12.
		$listener2 = function() {};
		$this->_instance->attach($listener2, 12);

		$this->assertEquals(-52, $this->_instance->getPriority($listener));
		$this->assertEquals(12, $this->_instance->getPriority($listener2));
	}

	/**
	 * Test the getPriority method with a non attached listener.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getPriority
	 */
	public function testGetPriorityWithoutListener()
	{
		$this->assertFalse($this->_instance->getPriority(new stdClass));
	}

	/**
	 * Test the getListeners method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getListeners
	 */
	public function testgetListeners()
	{
		// Attach a a few listeners.
		$listener = new stdClass;
		$this->_instance->attach($listener, 4);

		$listener1 = new stdClass;
		$this->_instance->attach($listener1, 3);

		$listener2 = new stdClass;
		$this->_instance->attach($listener2, 2);

		$listener3 = function() {};
		$this->_instance->attach($listener3, 1);

		$listener4 = function() {};
		$this->_instance->attach($listener4);

		// Get all listeners.
		$listeners = $this->_instance->getListeners();

		// Test they are sorted by priority.
		$this->assertSame($listener, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);
		$this->assertSame($listener2, $listeners[2]);
		$this->assertSame($listener3, $listeners[3]);
		$this->assertSame($listener4, $listeners[4]);
	}

	/**
	 * Test the getListeners method with an empty queue.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getListeners
	 */
	public function testgetListenersEmpty()
	{
		$this->assertEmpty($this->_instance->getListeners());
	}

	/**
	 * Test the getIterator method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getIterator
	 */
	public function testGetIterator()
	{
		// Attach a few listeners.
		$listener1 = new stdClass;
		$this->_instance->attach($listener1, 4);

		$listener2 = new stdClass;
		$this->_instance->attach($listener2, 3);

		$listener3 = new stdClass;
		$this->_instance->attach($listener3, 2);

		$listener4 = function() {};
		$this->_instance->attach($listener4, 1);

		$listener5 = function() {};
		$this->_instance->attach($listener5);

		// Get the inner queue.
		$iterator = $this->_instance->getIterator();

		$this->assertInstanceOf('SplPriorityQueue', $iterator);

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		// Assert they are sorted by priority.
		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
		$this->assertSame($listener3, $listeners[2]);
		$this->assertSame($listener4, $listeners[3]);
		$this->assertSame($listener5, $listeners[4]);
	}

	/**
	 * Test the getIterator method with some listeners
	 * having the same priority.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getIterator
	 */
	public function testGetIteratorSamePriority()
	{
		// Attach a listener with a priority 1.
		$listener1 = new stdClass;
		$this->_instance->attach($listener1, 1);

		// Attach a second listener with a priority 1.
		$listener2 = new stdClass;
		$this->_instance->attach($listener2, 1);

		// Attach a third listener with a priority 2.
		$listener3 = new stdClass;
		$this->_instance->attach($listener3, 2);

		// Get the inner queue.
		$iterator = $this->_instance->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		// Listeners with the same priority must be sorted
		// in the order they were added.
		$this->assertSame($listener3, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);
		$this->assertSame($listener2, $listeners[2]);
	}

	/**
	 * Test the getIterator method can be called multiple times.
	 * ListenerQueue is not a heap.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::getIterator
	 */
	public function testGetIteratorMultipleTimes()
	{
		// Attach a listener with a priority 1.
		$listener1 = new stdClass;
		$this->_instance->attach($listener1, 1);

		// Attach a second listener with a priority 2.
		$listener2 = new stdClass;
		$this->_instance->attach($listener2, 2);

		// Get the inner queue a first time.
		$iterator = $this->_instance->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		$this->assertSame($listener2, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);

		// Get the inner queue a second time.
		$iterator = $this->_instance->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		$this->assertSame($listener2, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);
	}

	/**
	 * Test the count method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Event\ListenerQueue::count
	 */
	public function testCount()
	{
		$this->assertCount(0, $this->_instance);

		// Attach two listeners.
		$listener = new stdClass;
		$this->_instance->attach($listener, 1);

		$listener1 = function() {};
		$this->_instance->attach($listener1, 2);

		$this->assertCount(2, $this->_instance);
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

		$this->_instance = new ListenerQueue;
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
