<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Input;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Input\Cli;
use Grisgris\Provider\Provider;

/**
 * Test case class for Cli.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
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
	 * @var    Provider  The provider object for constructing the Input.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `parseArguments` method with mixed arguments.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Cli::parseArguments
	 * @since   13.1
	 */
	public function testGet()
	{
		$_SERVER['argv'] = array('/dev/null', '--foo=bar', '-ab', 'blah');
		Reflection::invoke($this->_instance, 'parseArguments');
		$data = Reflection::getValue($this->_instance, 'data');

		$this->assertSame('bar', $data['foo']);
		$this->assertSame(true, $data['a']);
		$this->assertSame(true, $data['b']);

		$this->assertEquals(array('blah'), $this->_instance->args);
	}

	/**
	 * Tests the `get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Cli::get
	 * @since   13.1
	 */
	public function testParseLongArguments()
	{
		$_SERVER['argv'] = array('/dev/null', '--ab', 'cd', '--ef', '--gh=bam');
		Reflection::invoke($this->_instance, 'parseArguments');
		$data = Reflection::getValue($this->_instance, 'data');

		$this->assertSame('cd', $data['ab']);
		$this->assertSame(true, $data['ef']);
		$this->assertSame('bam', $data['gh']);

		$this->assertEquals(array(), $this->_instance->args);
	}

	/**
	 * Tests the `get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Cli::get
	 * @since   13.1
	 */
	public function testParseShortArguments()
	{
		$_SERVER['argv'] = array('/dev/null', '-ab', '-c', '-e', 'f', 'foobar', 'ghijk');
		Reflection::invoke($this->_instance, 'parseArguments');
		$data = Reflection::getValue($this->_instance, 'data');

		$this->assertSame(true, $data['a']);
		$this->assertSame(true, $data['b']);
		$this->assertSame(true, $data['c']);
		$this->assertSame('f', $data['e']);

		$this->assertEquals(array('foobar', 'ghijk'), $this->_instance->args);
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
		$this->_instance = new Cli($this->_provider, array());
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
