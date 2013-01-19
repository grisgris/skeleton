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

use Grisgris\Loader;
use Grisgris\Event\Dispatcher;
use Grisgris\Event\Event;

Loader::register('FooListener', __DIR__ . '/stubs/foolistener.php');
Loader::register('BarListener', __DIR__ . '/stubs/barlistener.php');

use stdClass;
use FooListener;
use BarListener;

/**
 * Test case class for Dispatcher.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class DispatcherTest extends TestCase
{
	/**
	 * @var    Dispatcher  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Test the registerEvent method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerEvent
	 */
	public function testRegisterEvent()
	{
		// Register the test event.
		$event = new Event('test');
		$this->_instance->registerEvent($event);

		// Register the foo event.
		$event1 = new Event('foo');
		$this->_instance->registerEvent($event1);

		$events = Reflection::getValue($this->_instance, 'events');

		$this->assertContains($event, $events);
		$this->assertContains($event1, $events);
	}

	/**
	 * Test the registerEvent method by reseting an existing event.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerEvent
	 */
	public function testRegisterEventReset()
	{
		// Register the test event.
		$event = new Event('test');
		$this->_instance->registerEvent($event);

		// Register the event one more time with a reset flag.
		$event1 = new Event('test', array('foo'));
		$this->_instance->registerEvent($event, true);

		$events = Reflection::getValue($this->_instance, 'events');

		$this->assertContainsOnly($event1, $events);
	}

	/**
	 * Test the unregisterEvent method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterEvent
	 */
	public function testUnregisterEvent()
	{
		// Register the test event.
		$event = new Event('test');
		$this->_instance->registerEvent($event);

		// Unregister it.
		$this->_instance->unregisterEvent($event);

		$events = Reflection::getValue($this->_instance, 'events');
		$this->assertEmpty($events);
	}

	/**
	 * Test the unregisterEvent method by using the event name.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterEvent
	 */
	public function testUnregisterEventByName()
	{
		// Register the test event.
		$event = new Event('test');
		$this->_instance->registerEvent($event);

		// Unregister it.
		$this->_instance->unregisterEvent('test');

		$events = Reflection::getValue($this->_instance, 'events');
		$this->assertEmpty($events);
	}

	/**
	 * Test the unregisterEvent method by unregistering
	 * a non existing event.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterEvent
	 */
	public function testUnregisterEventNonRegistered()
	{
		// Register the test event.
		$event = new Event('test');
		$this->_instance->registerEvent($event);

		// Unregister an unexisting event.
		$this->_instance->unregisterEvent('foo');

		$events = Reflection::getValue($this->_instance, 'events');

		// Assert the test event is still here.
		$this->assertContainsOnly($event, $events);
	}

	/**
	 * Test the registerListener method without specified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerWithoutSpecifiedEvents()
	{
		$listener = new FooListener;

		$this->_instance->registerListener($listener);

		// Assert the listener has been registered to all events.
		$this->assertTrue($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->_instance->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method with specified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerWithSpecifiedEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onSomething',
		);

		$this->_instance->registerListener($listener, $eventNames);

		// Assert the listener has been registered to the onBeforeSomething and onSomething events only.
		$this->assertTrue($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->_instance->hasListener($listener, 'onSomething'));

		$this->assertFalse($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method with specified priorities / event,
	 * but unspecified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerWithPrioritiesWithoutEvents()
	{
		$listener = new FooListener;

		$priorities = array(
			'onBeforeSomething' => 8,
			'onSomething' => -50
		);

		$this->_instance->registerListener($listener, array(), $priorities);

		// Assert the listener has been registered to the onBeforeSomething and onSomething events only.
		$this->assertTrue($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->_instance->hasListener($listener, 'onSomething'));

		// Assert the listener is correctly registered with the given priority.
		$this->assertEquals(8, $this->_instance->getListenerPriority($listener, 'onBeforeSomething'));
		$this->assertEquals(-50, $this->_instance->getListenerPriority($listener, 'onSomething'));
	}

	/**
	 * Test the registerListener method with specified priorities / event,
	 * and specified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerWithPrioritiesWithEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething'
		);

		$priorities = array(
			'onBeforeSomething' => 8,
			'onSomething' => -50
		);

		$this->_instance->registerListener($listener, $eventNames, $priorities);

		// Assert the listener has been registered to the onBeforeSomething event only.
		$this->assertTrue($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->_instance->hasListener($listener, 'onSomething'));

		// Assert the listener is correctly registered with the given priority.
		$this->assertEquals(8, $this->_instance->getListenerPriority($listener, 'onBeforeSomething'));
	}

	/**
	 * Test the registerListener method with an invalid specified event name.
	 * (the event name doesn't match any listener method).
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerInvalidSpecifiedEvent()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onNothing',
		);

		$this->_instance->registerListener($listener, $eventNames);

		// Assert the listener is not registered.
		$this->assertFalse($this->_instance->hasListener($listener));
	}

	/**
	 * Test the registerListener method for a closure listener.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerClosure()
	{
		$listener = function (Event $e) {};

		$this->_instance->registerListener($listener, array('onSomething', 'onAfterSomething'));

		$this->assertTrue($this->_instance->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method for a closure listener.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 */
	public function testRegisterListenerClosureWithPriority()
	{
		$listener = function (Event $e) {};

		$this->_instance->registerListener($listener, array('onSomething'), array('onSomething' => 122));

		$this->assertTrue($this->_instance->hasListener($listener, 'onSomething'));
		$this->assertEquals(122, $this->_instance->getListenerPriority($listener, 'onSomething'));
	}

	/**
	 * Test the registerListener exception because of
	 * unspecified event name for a closure listener.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testRegisterListenerClosureException()
	{
		$listener = function (Event $e) {};

		$this->_instance->registerListener($listener);
	}

	/**
	 * Test the registerListener exception
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::registerListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testRegisterListenerException()
	{
		$this->_instance->registerListener('foo');
	}

	/**
	 * Test the unregisterListener method without specified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterListener
	 */
	public function testUnregisterListenerWithoutSpecifiedEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->_instance->registerListener($listener, $eventNames);

		// Unregister the listener.
		$this->_instance->unregisterListener($listener);

		// Assert the listener has been unregistered from these 2 events.
		$this->assertFalse($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method without specified event names
	 * and a closure listener.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterListener
	 */
	public function testUnregisterListenerClosureWithoutSpecifiedEvents()
	{
		$listener = function (Event $e) {};

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->_instance->registerListener($listener, $eventNames);

		// Unregister the listener.
		$this->_instance->unregisterListener($listener);

		// Assert the listener has been unregistered from these 2 events.
		$this->assertFalse($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method with specified event names.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterListener
	 */
	public function testUnregisterListenerWithEvent()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->_instance->registerListener($listener, $eventNames);

		// Unregister the listener from the onAfterSomething event.
		$this->_instance->unregisterListener($listener, array('onAfterSomething'));

		// Assert the listener has been unregistered only from the onAfterSomething event.
		$this->assertTrue($this->_instance->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->_instance->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method exception.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::unregisterListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testUnregisterListenerException()
	{
		$this->_instance->unregisterListener('foo');
	}

	/**
	 * Test the getListeners method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::getListeners
	 */
	public function testGetListeners()
	{
		// Register two listeners for the onBeforeSomething event.
		$listener1 = new FooListener;
		$listener2 = function (Event $e) {};

		$this->_instance->registerListener($listener1, array('onBeforeSomething'))
			->registerListener($listener2, array('onBeforeSomething'));

		// Get the event listeners.
		$listeners = $this->_instance->getListeners('onBeforeSomething');

		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
	}

	/**
	 * Test the getListeners method by using an event object.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::getListeners
	 */
	public function testGetListenersEventObject()
	{
		// Register two listeners for the onBeforeSomething event.
		$listener1 = new FooListener;
		$listener2 = function (Event $e) {};

		$this->_instance->registerListener($listener1, array('onBeforeSomething'))
			->registerListener($listener2, array('onBeforeSomething'));

		// Get the listeners using an event object.
		$listeners = $this->_instance->getListeners(new Event('onBeforeSomething'));

		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
	}

	/**
	 * Test the getListeners method default value.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::getListeners
	 */
	public function testGetListenersDefault()
	{
		$this->assertEmpty($this->_instance->getListeners('unexisting'));
	}

	/**
	 * Test the getListenerPriority method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::getListenerPriority
	 */
	public function testGetListenerPriority()
	{
		// Register a listener with some priorities.
		$listener1 = new FooListener;

		$this->_instance->registerListener($listener1,
			array('onBeforeSomething', 'onAfterSomething'),
			array('onBeforeSomething' => 22, 'onAfterSomething' => -100)
		);

		$listener2 = function (Event $e) {};
		$this->_instance->registerListener($listener2,
			array('onBeforeSomething'),
			array('onBeforeSomething' => 114)
		);

		$this->assertEquals(22, $this->_instance->getListenerPriority($listener1, 'onBeforeSomething'));
		$this->assertEquals(-100, $this->_instance->getListenerPriority($listener1, 'onAfterSomething'));
		$this->assertEquals(114, $this->_instance->getListenerPriority($listener2, 'onBeforeSomething'));
	}

	/**
	 * Test the getListenerPriority default value.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::getListenerPriority
	 */
	public function testGetListenerPriorityDefault()
	{
		$this->assertFalse($this->_instance->getListenerPriority(new stdClass, 'onSomething'));
	}

	/**
	 * Test the countListeners method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::countListeners
	 */
	public function testCountListeners()
	{
		$listener1 = new FooListener;
		$listener2 = new BarListener;
		$listener3 = function (Event $e) {};

		$this->_instance->registerListener($listener1, array('onBeforeSomething'));
		$this->_instance->registerListener($listener2, array('onBeforeSomething'));
		$this->_instance->registerListener($listener3, array('onBeforeSomething'));

		$this->assertEquals(3, $this->_instance->countListeners('onBeforeSomething'));
		$this->assertEquals(3, $this->_instance->countListeners(new Event('onBeforeSomething')));
	}

	/**
	 * Test the countListeners method default value.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::countListeners
	 */
	public function testCountListenersDefault()
	{
		$this->assertEquals(0, $this->_instance->countListeners('onSomething'));
	}

	/**
	 * Test the hasListener method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::hasListener
	 */
	public function testHasListener()
	{
		$listener1 = new FooListener;

		$this->_instance->registerListener($listener1, array('onBeforeSomething'));

		$this->assertTrue($this->_instance->hasListener($listener1));
		$this->assertTrue($this->_instance->hasListener($listener1, 'onBeforeSomething'));
		$this->assertTrue($this->_instance->hasListener($listener1, new Event('onBeforeSomething')));
	}

	/**
	 * Test the hasListener method default value.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::hasListener
	 */
	public function testHasListenerDefault()
	{
		$this->assertFalse($this->_instance->hasListener(new stdClass, 'onSomething'));
		$this->assertFalse($this->_instance->hasListener(new stdClass, new Event('onSomething')));
	}

	/**
	 * Test the triggerEvent method.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEvent()
	{
		$mockListener1 = $this->getMock('FooListener');
		$mockListener1->expects($this->once())
			->method('onBeforeSomething');

		$mockListener2 = $this->getMock('BarListener');
		$mockListener2->expects($this->once())
			->method('onBeforeSomething');

		$invoked = 0;
		$listener3 = function (Event $e) use (&$invoked) {
			$invoked++;
		};

		$this->_instance->registerListener($mockListener1, array('onBeforeSomething'));
		$this->_instance->registerListener($mockListener2, array('onBeforeSomething'));
		$this->_instance->registerListener($listener3, array('onBeforeSomething'));

		$this->_instance->triggerEvent('onBeforeSomething');

		$this->assertEquals(1, $invoked);
	}

	/**
	 * Test the triggerEvent method with a specified priority.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEventWithPriority()
	{
		// The listener 1 will manipulate the foo argument $foo[] = 1
		$listener1 = new FooListener;

		// The listener 2 will manipulate the foo argument $foo[] = 2
		$listener2 = new BarListener;

		// The listener 3 will manipulate the foo argument $foo[] = 3
		$listener3 = function (Event $e)
		{
			$foo = $e->getArgument('foo');
			$foo[] = 3;
			$e->setArgument('foo', $foo);
		};

		// The listener 4 will manipulate the foo argument $foo[] = 4
		$listener4 = function (Event $e)
		{
			$foo = $e->getArgument('foo');
			$foo[] = 4;
			$e->setArgument('foo', $foo);
		};

		$this->_instance->registerListener($listener1, array('onBeforeSomething'), array('onBeforeSomething' => 3));
		$this->_instance->registerListener($listener2, array('onBeforeSomething'), array('onBeforeSomething' => 2));
		$this->_instance->registerListener($listener3, array('onBeforeSomething'), array('onBeforeSomething' => 1));
		$this->_instance->registerListener($listener4, array('onBeforeSomething'));

		// Create an event with an empty array as foo argument.
		$event = new Event('onBeforeSomething');
		$event->setArgument('foo', array());

		// Trigger the event.
		$event = $this->_instance->triggerEvent($event);

		// Assert the listeners were called in the expected order.
		$foo = $event->getArgument('foo');
		$this->assertEquals(array(1, 2, 3, 4), $foo);
	}

	/**
	 * Test the triggerEvent method with a listener stopping the event propagation.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEventPropagationStopped()
	{
		$listener1 = new FooListener;

		// This listener will stop the event propagation.
		$listener2 = new BarListener;

		$invoked = 0;
		$listener3 = function (Event $e) use (&$invoked) {
			$invoked++;
		};

		$this->_instance->registerListener($listener1, array('onSomething'), array('onSomething' => 3));
		$this->_instance->registerListener($listener2, array('onSomething'), array('onSomething' => 2));
		$this->_instance->registerListener($listener3, array('onSomething'), array('onSomething' => 1));

		$this->_instance->triggerEvent('onSomething');

		// The listener 2 will stop the event propagation.
		// We don't expect the listener 3 to be called.
		$this->assertEquals(0, $invoked);
	}

	/**
	 * Test the triggerEvent method with a registered event object.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEventRegistered()
	{
		// Register a custom event.
		$event = new Event('onBeforeSomething');
		$this->_instance->registerEvent($event);

		$listener1 = new FooListener;
		$this->_instance->registerListener($listener1, array('onBeforeSomething'));

		$eventReturned = $this->_instance->triggerEvent('onBeforeSomething');

		$this->assertSame($event, $eventReturned);
	}

	/**
	 * Test the triggerEvent method with a registered event object.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEventObject()
	{
		$listener1 = new FooListener;

		$this->_instance->registerListener($listener1, array('onBeforeSomething'));

		// Trigger the event by passing a custom object.
		$event = new Event('onBeforeSomething');
		$eventReturned = $this->_instance->triggerEvent($event);

		$this->assertSame($event, $eventReturned);
	}

	/**
	 * Test the triggerEvent method with a non registered event.
	 *
	 * @since   13.1
	 *
	 * @covers  Grisgris\Event\Dispatcher::triggerEvent
	 */
	public function testTriggerEventDefault()
	{
		$this->assertInstanceOf('\\Grisgris\\Event\\Event', $this->_instance->triggerEvent('onTest'));
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

		$this->_instance = new Dispatcher;
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
