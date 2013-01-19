<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Provider
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Provider;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Loader;
use Grisgris\Provider\Provider;

Loader::register('Grisgris\Test\Suites\Unit\Provider\Foobar', __DIR__ . '/stubs/foobar.php');
Loader::register('Grisgris\Test\Suites\Unit\Provider\Invokable', __DIR__ . '/stubs/invokable.php');
Loader::register('Grisgris\Test\Suites\Unit\Provider\Noninvokable', __DIR__ . '/stubs/noninvokable.php');

/**
 * Tests for Provider class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Provider
 * @since       13.1
 */
class ProviderTest extends TestCase
{
	/**
	 * @var    Provider  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Testing `get` and `set` with a string.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 * @covers  Grisgris\Provider\Provider::set
	 */
	public function testGetSetWithString()
	{
		$this->_instance->set('foo', 'bar');
		$this->assertEquals('bar', $this->_instance->get('foo'));
	}

	/**
	 * Testing `get` and `set` with a closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 * @covers  Grisgris\Provider\Provider::set
	 */
	public function testGetSetWithClosure()
	{
		$this->_instance->set('foobar', function ()
		{
			return new Foobar;
		});

		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $this->_instance->get('foobar'));
	}

	/**
	 * Testing `get` and `set` with a closure where two `get` calls return different objects.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 * @covers  Grisgris\Provider\Provider::set
	 */
	public function testFoobarsShouldBeDifferentForDifferentGets()
	{
		$this->_instance->set('foobar', function ()
		{
			return new Foobar;
		});

		$one = $this->_instance->get('foobar');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $one);

		$two = $this->_instance->get('foobar');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $two);

		$this->assertNotSame($one, $two);
	}

	/**
	 * Testing `get` to ensure that the provider is passed as the first argument to closures.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 */
	public function testGetShouldPassProviderAsArgument()
	{
		$this->_instance->set('foobar', function ()
		{
			return new Foobar;
		});

		$this->_instance->set('provider', function ($provider)
		{
			return $provider;
		});

		$this->assertNotSame($this->_instance, $this->_instance->get('foobar'));
		$this->assertSame($this->_instance, $this->_instance->get('provider'));
	}

	/**
	 * Testing `setShared` to ensure that the returned object is always the same.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::setShared
	 */
	public function testSetShared()
	{
		$this->_instance->setShared('foobar.shared', function ()
		{
			return new Foobar;
		});

		$one = $this->_instance->get('foobar.shared');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $one);

		$two = $this->_instance->get('foobar.shared');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $two);

		$this->assertSame($one, $two);
	}

	/**
	 * Testing `getRaw` to ensure that the returned value is exactly what was set.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::getRaw
	 */
	public function testGetRaw()
	{
		$closure = function ()
		{
			return 'foo';
		};
		$this->_instance->set('closure', $closure);

		$this->assertSame($closure, $this->_instance->getRaw('closure'));
	}

	/**
	 * Testing `getRaw` to ensure that the returned value is null if null was set.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::getRaw
	 */
	public function testGetRawHonorsNullValues()
	{
		$this->_instance->set('foobar', null);
		$this->assertNull($this->_instance->getRaw('foobar'));
	}

	/**
	 * Testing the `extend` method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::extend
	 */
	public function testExtend()
	{
		$this->_instance->setShared('foobar.shared', function ()
		{
			return new Foobar;
		});

		$value = 42;

		$this->_instance->extend('foobar.shared', function ($sharedFoobar) use($value)
		{
			$sharedFoobar->value = $value;
			return $sharedFoobar;
		});

		$one = $this->_instance->get('foobar.shared');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $one);
		$this->assertEquals($value, $one->value);

		$two = $this->_instance->get('foobar.shared');
		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Foobar', $two);
		$this->assertEquals($value, $two->value);

		$this->assertSame($one, $two);
	}

	/**
	 * Testing the `extend` method to ensure that a valid key is present to extend.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::extend
	 */
	public function testExtendValidatesKeyIsPresent()
	{
		$this->setExpectedException(
			'InvalidArgumentException',
			'This provider does not contain a service closure `foo` to extend.'
		);

		$this->_instance->extend('foo', function ()
		{
		});
	}

	/**
	 * Testing the `extend` method to ensure that a valid key is present to extend.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::extend
	 */
	public function testExtendValidatesKeyIsClosure()
	{
		$this->setExpectedException(
			'InvalidArgumentException',
			'This provider does not contain a service closure `foo` to extend.'
		);

		$this->_instance->set('foo', 42);
		$this->_instance->extend('foo', function ()
		{
		});
	}

	/**
	 * Testing the `get` method with Invokable classes.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 */
	public function testGetInvokableObjectExecutesObject()
	{
		$this->_instance->set('invokable', new Invokable);

		$this->assertEquals('Boo!!!', $this->_instance->get('invokable'));
	}

	/**
	 * Testing the `get` method with Invokable classes.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  Grisgris\Provider\Provider::get
	 */
	public function testGetNoninvokableObjectReturnsObject()
	{
		$this->_instance->set('noninvokable', new Noninvokable);

		$this->assertInstanceOf('Grisgris\Test\Suites\Unit\Provider\Noninvokable', $this->_instance->get('noninvokable'));
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

		$this->_instance = new Provider();
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
