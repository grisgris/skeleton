<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Application;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Application\Application;
use Grisgris\Provider\Provider;
use Grisgris\Registry\Registry;

/**
 * Test case class for Application.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class ApplicationTest extends TestCase
{
	/**
	 * @var    Application  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Application.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `__construct` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::__construct
	 * @since   13.1
	 */
	public function test__constructWithPrimedProvider()
	{
		$dispatcher = $this->getMock('Grisgris\Event\Dispatcher');
		$logger = $this->getMock('Grisgris\Log\Logger');
		$input = $this->getMock('Grisgris\Input\Input');
		$application = $this->getMockForAbstractClass('Grisgris\Application\Application', array(), '', false);

		$provider = new Provider;
		$provider->set('dispatcher', $dispatcher);
		$provider->set('logger', $logger);
		$provider->set('input', $input);
		$provider->set('application', $application);

		$this->_instance->__construct($provider);

		$this->assertSame(
			$provider,
			Reflection::getValue($this->_instance, 'provider')
		);
		$this->assertSame(
			$dispatcher,
			Reflection::getValue($this->_instance, 'dispatcher')
		);
		$this->assertSame(
			$logger,
			Reflection::getValue($this->_instance, 'logger')
		);
		$this->assertSame(
			$input,
			Reflection::getValue($this->_instance, 'input')
		);
		$this->assertSame(
			$application,
			Reflection::getValue($this->_instance, 'provider')->get('application')
		);
	}

	/**
	 * Tests the `__construct` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::__construct
	 * @since   13.1
	 */
	public function test__constructWithEmptyProvider()
	{
		$this->_instance->__construct();

		$this->assertInstanceOf(
			'Grisgris\Provider\Provider',
			Reflection::getValue($this->_instance, 'provider')
		);
		$this->assertInstanceOf(
			'Grisgris\Event\Dispatcher',
			Reflection::getValue($this->_instance, 'dispatcher')
		);
		$this->assertInstanceOf(
			'Grisgris\Log\Logger',
			Reflection::getValue($this->_instance, 'logger')
		);
		$this->assertInstanceOf(
			'Grisgris\Input\Input',
			Reflection::getValue($this->_instance, 'input')
		);
		$this->assertSame(
			$this->_instance,
			Reflection::getValue($this->_instance, 'provider')->get('application')
		);
	}

	/**
	 * Tests the `get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::get
	 * @since   13.1
	 */
	public function testGet()
	{
		$tmp = new Registry(
			array(
				'foo' => 'bar',
				'bar' => array('foo' => 'baz')
			)
		);
		Reflection::setValue($this->_instance, 'config', $tmp);

		$this->assertEquals(
			'bar',
			$this->_instance->get('foo', 'car'),
			'Checks a known configuration setting is returned.'
		);

		$this->assertEquals(
			'car',
			$this->_instance->get('goo', 'car'),
			'Checks an unknown configuration setting returns the default.'
		);

		$this->assertEquals(
			'baz',
			$this->_instance->get('bar.foo', 'car'),
			'Checks a known configuration setting with added depth is returned.'
		);
	}

	/**
	 * Tests the `getConfig` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::getConfig
	 * @since   13.1
	 */
	public function testGetConfig()
	{
		$tmp = new Registry(array('foo' => 'bar'));
		Reflection::setValue($this->_instance, 'config', $tmp);

		$this->assertSame($tmp, $this->_instance->getConfig());
	}

	/**
	 * Tests the `getLogger` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::getLogger
	 * @since   13.1
	 */
	public function testGetLogger()
	{
		$tmp = $this->getMock('Grisgris\Logger\Logger');
		Reflection::setValue($this->_instance, 'logger', $tmp);

		$this->assertSame($tmp, $this->_instance->getLogger());
	}

	/**
	 * Tests the `loadConfiguration` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::loadConfiguration
	 * @since   13.1
	 */
	public function testLoadConfiguration()
	{
		$tmp = new Registry(array('foo' => 'bar'));
		Reflection::setValue($this->_instance, 'config', $tmp);

		$this->assertSame(
			$this->_instance,
			$this->_instance->loadConfiguration(array()),
			'Check method chaining.'
		);

		$this->_instance->loadConfiguration(
			array(
				'goo' => 'car',
				'foo' => 'baz'
			)
		);

		$this->assertEquals(
			'car',
			$this->_instance->getConfig()->get('goo')
		);
		$this->assertEquals(
			'baz',
			$this->_instance->getConfig()->get('foo')
		);
		$this->assertEquals(
			'',
			$this->_instance->getConfig()->get('blah')
		);

		// Make sure we can load objects too.
		$this->_instance->loadConfiguration(
			(object) array(
				'goo' => 'berflasm'
			)
		);
		$this->assertEquals(
			'berflasm',
			$this->_instance->getConfig()->get('goo')
		);
	}

	/**
	 * Tests the `registerListener` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::registerListener
	 * @since   13.1
	 */
	public function testRegisterListener()
	{
		$listener = function(){};
		$events = array(1);
		$priorities = array(2);

		// Create a dispatcher mock.
		$dispatcher = $this->getMock('Grisgris\Event\Dispatcher', array('registerListener'));
		$dispatcher->expects($this->once())->method('registerListener')->with(
			$this->identicalTo($listener),
			$this->identicalTo($events),
			$this->identicalTo($priorities)
		);
		Reflection::setValue($this->_instance, 'dispatcher', $dispatcher);

		$this->_instance->registerListener($listener, $events, $priorities);
	}

	/**
	 * Tests the `set` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::set
	 * @since   13.1
	 */
	public function testSet()
	{
		$tmp = new Registry(
			array(
				'foo' => 'bar',
				'bar' => array('foo' => 'baz')
			)
		);
		Reflection::setValue($this->_instance, 'config', $tmp);

		$this->assertEquals(
			'bar',
			$this->_instance->set('foo', 'car'),
			'Checks set returns the previous value.'
		);

		$this->assertEquals(
			'car',
			$tmp->get('foo'),
			'Checks the new value has been set.'
		);
	}

	/**
	 * Tests the `triggerEvent` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::triggerEvent
	 * @since   13.1
	 */
	public function testTriggerEvent()
	{
		$event = $this->getMock('Grisgris\Event\Event', array(), array(), '', false);

		// If we trigger an event when no dispatcher exists the response should be null.
		$this->assertNull($this->_instance->triggerEvent($event));

		// Create a dispatcher mock.
		$dispatcher = $this->getMock('Grisgris\Event\Dispatcher', array('triggerEvent'));
		$dispatcher->expects($this->once())->method('triggerEvent')->with(
			$this->identicalTo($event)
		);
		Reflection::setValue($this->_instance, 'dispatcher', $dispatcher);

		$this->_instance->triggerEvent($event);
	}

	/**
	 * Tests the `fetchDispatcher` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchDispatcher
	 * @since   13.1
	 */
	public function testFetchDispatcherWithPrimedProvider()
	{
		$mock = $this->getMock('Grisgris\Event\Dispatcher');
		$this->_provider->set('dispatcher', $mock);

		$this->assertSame(
			$mock,
			Reflection::invoke($this->_instance, 'fetchDispatcher')
		);
	}

	/**
	 * Tests the `fetchDispatcher` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchDispatcher
	 * @since   13.1
	 */
	public function testFetchDispatcherWithEmptyProvider()
	{
		$mock = $this->getMock('Grisgris\Event\Dispatcher');
		$this->_provider->set('dispatcher', null);

		$actual = Reflection::invoke($this->_instance, 'fetchDispatcher');
		$this->assertInstanceOf('Grisgris\Event\Dispatcher', $actual);
		$this->assertNotSame($mock, $actual);
	}

	/**
	 * Tests the `fetchInput` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchInput
	 * @since   13.1
	 */
	public function testFetchInputWithPrimedProvider()
	{
		$mock = $this->getMock('Grisgris\Input\Input');
		$this->_provider->set('input', $mock);

		$this->assertSame(
			$mock,
			Reflection::invoke($this->_instance, 'fetchInput')
		);
	}

	/**
	 * Tests the `fetchInput` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchInput
	 * @since   13.1
	 */
	public function testFetchInputWithEmptyProvider()
	{
		$mock = $this->getMock('Grisgris\Input\Input');
		$this->_provider->set('input', null);

		$actual = Reflection::invoke($this->_instance, 'fetchInput');
		$this->assertInstanceOf('Grisgris\Input\Input', $actual);
		$this->assertNotSame($mock, $actual);
	}

	/**
	 * Tests the `fetchLogger` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchLogger
	 * @since   13.1
	 */
	public function testFetchLoggerWithPrimedProvider()
	{
		$mock = $this->getMock('Grisgris\Log\Logger');
		$this->_provider->set('logger', $mock);

		$this->assertSame(
			$mock,
			Reflection::invoke($this->_instance, 'fetchLogger')
		);
	}

	/**
	 * Tests the `fetchLogger` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Application::fetchLogger
	 * @since   13.1
	 */
	public function testFetchLoggerWithEmptyProvider()
	{
		$mock = $this->getMock('Grisgris\Log\Logger');
		$this->_provider->set('logger', null);

		$actual = Reflection::invoke($this->_instance, 'fetchLogger');
		$this->assertInstanceOf('Grisgris\Log\Logger', $actual);
		$this->assertNotSame($mock, $actual);
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

		$this->_provider = new Provider;
		$this->_instance = $this->getMockForAbstractClass('Grisgris\Application\Application', array(), '', false);
		Reflection::setValue($this->_instance, 'provider', $this->_provider);
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
		$this->_provider = null;

		parent::tearDown();
	}
}
