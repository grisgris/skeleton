<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Router;

use Grisgris\Loader;
use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;
use Grisgris\Router\Router;
use Grisgris\Provider\Provider;

Loader::register('TControllerBar', __DIR__ . '/stubs/bar.php');
Loader::register('MyTestControllerBaz', __DIR__ . '/stubs/baz.php');
Loader::register('MyTestController\\Foo', __DIR__ . '/stubs/foo.php');

/**
 * Test class for Router.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 * @since       13.1
 */
class RouterTest extends TestCase
{
	/**
	 * @var    Router  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `__construct` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::__construct
	 * @since   13.1
	 */
	public function test__construct()
	{
		$this->assertAttributeInstanceOf('\\Grisgris\\Provider\\Provider', 'provider', $this->_instance);
	}

	/**
	 * Tests the `setControllerNamespace` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::setControllerNamespace
	 * @since   13.1
	 */
	public function testSetControllerNamespace()
	{
		$this->_instance->setControllerNamespace('MyApplication');
		$this->assertAttributeEquals('MyApplication', 'controllerNamespace', $this->_instance);
	}

	/**
	 * Tests the `setDefaultController` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::setDefaultController
	 * @since   13.1
	 */
	public function testSetDefaultController()
	{
		$this->_instance->setDefaultController('foobar');
		$this->assertAttributeEquals('foobar', 'default', $this->_instance);
	}

	/**
	 * Tests the `fetchController` method if the controller class is missing.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::fetchController
	 * @since   13.1
	 */
	public function testFetchControllerWithMissingClass()
	{
		$this->setExpectedException('RuntimeException');
		$controller = Reflection::invoke($this->_instance, 'fetchController', 'goober');
	}

	/**
	 * Tests the `fetchController` method if the class not a controller.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::fetchController
	 * @since   13.1
	 */
	public function testFetchControllerWithNonController()
	{
		$this->setExpectedException('RuntimeException');
		$controller = Reflection::invoke($this->_instance, 'fetchController', 'MyTestControllerBaz');
	}

	/**
	 * Tests the `fetchController` method with a prefix set.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::fetchController
	 * @since   13.1
	 */
	public function testFetchControllerWithNamespaceSet()
	{
		Reflection::setValue($this->_instance, 'controllerNamespace', 'MyTestController');
		$controller = Reflection::invoke($this->_instance, 'fetchController', 'foo');
	}

	/**
	 * Tests the `fetchController` method without a prefix set even though it is necessary.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::fetchController
	 * @since   13.1
	 */
	public function testFetchControllerWithoutPrefixSetThoughNecessary()
	{
		$this->setExpectedException('RuntimeException');
		$controller = Reflection::invoke($this->_instance, 'fetchController', 'foo');
	}

	/**
	 * Tests the `fetchController` method without a prefix set.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Router::fetchController
	 * @since   13.1
	 */
	public function testFetchControllerWithoutPrefixSet()
	{
		$controller = Reflection::invoke($this->_instance, 'fetchController', 'TControllerBar');
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

		$this->_instance = $this->getMockForAbstractClass('\\Grisgris\\Router\\Router', array(new Provider));
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
