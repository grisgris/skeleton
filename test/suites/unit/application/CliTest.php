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

use Grisgris\Application\Cli;
use Grisgris\Provider\Provider;

/**
 * Test case class for Cli.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class CliTest extends TestCase
{
	/**
	 * @var    Cli  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Application.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `fetchInput` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Cli::fetchInput
	 * @since   13.1
	 */
	public function testFetchInputWithPrimedProvider()
	{
		$mock = $this->getMock('Grisgris\Input\Cli');
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
	 * @covers  Grisgris\Application\Cli::fetchInput
	 * @since   13.1
	 */
	public function testFetchInputWithEmptyProvider()
	{
		$mock = $this->getMock('Grisgris\Input\Cli');
		$this->_provider->set('input', null);

		$actual = Reflection::invoke($this->_instance, 'fetchInput');
		$this->assertInstanceOf('Grisgris\Input\Cli', $actual);
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
		$this->_instance = $this->getMockForAbstractClass('Grisgris\Application\Cli', array(), '', false);
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
