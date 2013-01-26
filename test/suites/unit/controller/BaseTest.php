<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Controller;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Controller\Base;
use Grisgris\Provider\Provider;

/**
 * Test case class for Base.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 * @since       13.1
 */
class BaseTest extends TestCase
{
	/**
	 * @var    Base  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the object.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `__construct` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Controller\Base::__construct
	 * @since   13.1
	 */
	public function test__construct()
	{
		$this->_provider->set('input', 'foo');
		$this->_provider->set('application', 'bar');

		$this->_instance->__construct($this->_provider);

		$this->assertEquals('foo', $this->_instance->getInput());
		$this->assertEquals('bar', $this->_instance->getApplication());
		$this->assertAttributeSame($this->_provider, 'provider', $this->_instance);
	}

	/**
	 * Tests the `getApplication` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Controller\Base::getApplication
	 * @since   13.1
	 */
	public function testGetApplication()
	{
		Reflection::setValue($this->_instance, 'application', 'application');

		$this->assertEquals('application', $this->_instance->getApplication());
	}

	/**
	 * Tests the `getInput` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Controller\Base::getInput
	 * @since   13.1
	 */
	public function testGetInput()
	{
		Reflection::setValue($this->_instance, 'input', 'input');

		$this->assertEquals('input', $this->_instance->getInput());
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
		$this->_instance = $this->getMockBuilder('Grisgris\Controller\Base')
			->disableOriginalConstructor()
			->setMethods(array('execute'))
			->getMock();

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
