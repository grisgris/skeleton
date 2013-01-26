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

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;
use Grisgris\Router\Rest;
use Grisgris\Input\Input;
use Grisgris\Provider\Provider;

/**
 * Test class for Rest.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 * @since       13.1
 */
class RestTest extends TestCase
{
	/**
	 * @var    Rest  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    string  The server REQUEST_METHOD cached to keep it clean.
	 * @since  13.1
	 */
	private $_method;

	/**
	 * Tests the setHttpMethodSuffix method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Rest::setHttpMethodSuffix
	 * @since   13.1
	 */
	public function testSetHttpMethodSuffix()
	{
		$this->_instance->setHttpMethodSuffix('FOO', 'Bar');
		$s = Reflection::getValue($this->_instance, 'suffixMap');
		$this->assertEquals('Bar', $s['FOO']);
	}

	/**
	 * Tests the fetchControllerSuffix method if the suffix map is missing.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Rest::fetchControllerSuffix
	 * @since   13.1
	 */
	public function testFetchControllerSuffixWithMissingSuffixMap()
	{
		$_SERVER['REQUEST_METHOD'] = 'FOOBAR';

		$this->setExpectedException('RuntimeException');
		$suffix = Reflection::invoke($this->_instance, 'fetchControllerSuffix');
	}

	/**
	 * Provides test data for testing fetch controller sufix
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public function seedFetchControllerSuffixData()
	{
		// Input, Expected
		return array(
			// Don't allow method in POST request
			array('GET', 'Get', null, false),
			array('POST', 'Create', "get", false),
			array('POST', 'Create', null, false),
			array('POST', 'Create', "post", false),
			array('PUT', 'Update', null, false),
			array('POST', 'Create', "put", false),
			array('PATCH', 'Update', null, false),
			array('POST', 'Create', "patch", false),
			array('DELETE', 'Delete', null, false),
			array('POST', 'Create', "delete", false),
			array('HEAD', 'Head', null, false),
			array('POST', 'Create', "head", false),
			array('OPTIONS', 'Options', null, false),
			array('POST', 'Create', "options", false),
			array('POST', 'Create', "foo", false),
			array('FOO', 'Create', "foo", true),

			// Allow method in POST request
			array('GET', 'Get', null, false, true),
			array('POST', 'Get', "get", false, true),
			array('POST', 'Create', null, false, true),
			array('POST', 'Create', "post", false, true),
			array('PUT', 'Update', null, false, true),
			array('POST', 'Update', "put", false, true),
			array('PATCH', 'Update', null, false, true),
			array('POST', 'Update', "patch", false, true),
			array('DELETE', 'Delete', null, false, true),
			array('POST', 'Delete', "delete", false, true),
			array('HEAD', 'Head', null, false, true),
			array('POST', 'Head', "head", false, true),
			array('OPTIONS', 'Options', null, false, true),
			array('POST', 'Options', "options", false, true),
			array('POST', 'Create', "foo", false, true),
			array('FOO', 'Create', "foo", true, true),
		);
	}

	/**
	 * Tests the fetchControllerSuffix method.
	 *
	 * @param   string   $input        Input string to test.
	 * @param   string   $expected     Expected fetched string.
	 * @param   mixed    $method       Method to override POST request
	 * @param   boolean  $exception    True if an RuntimeException is expected based on invalid input
	 * @param   boolean  $allowMethod  Allow or not to pass method in post request as parameter
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Router\Rest::fetchControllerSuffix
	 * @dataProvider  seedFetchControllerSuffixData
	 * @since         13.1
	 */
	public function testFetchControllerSuffix($input, $expected, $method, $exception, $allowMethod=false)
	{
		Reflection::invoke($this->_instance, 'setMethodInPostVars', $allowMethod);

		// Set reuqest method
		$_SERVER['REQUEST_METHOD'] = $input;

		// Set method in POST request
		$_GET['_method'] = $method;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('RuntimeException');
		}

		// Execute the code to test.
		$actual = Reflection::invoke($this->_instance, 'fetchControllerSuffix');

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Tests the setMethodInPostVars and isMethodInPostVars.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Router\Rest::setMethodInPostVars
	 * @covers  Grisgris\Router\Rest::isMethodInPostVars
	 * @since   13.1
	 */
	public function testMethodInPostRequest()
	{
		// Check the defaults
		$this->assertEquals(false, Reflection::invoke($this->_instance, 'isMethodInPostVars'));

		// Check setting true
		Reflection::invoke($this->_instance, 'setMethodInPostVars', true);
		$this->assertEquals(true, Reflection::invoke($this->_instance, 'isMethodInPostVars'));

		// Check setting false
		Reflection::invoke($this->_instance, 'setMethodInPostVars', false);
		$this->assertEquals(false, Reflection::invoke($this->_instance, 'isMethodInPostVars'));
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

		$provider = new Provider;
		$provider->set('input', new Input($provider, array()));
		$this->_instance = new Rest($provider);
		$this->_method = @$_SERVER['REQUEST_METHOD'];
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
		$_SERVER['REQUEST_METHOD'] = $this->_method;

		parent::tearDown();
	}
}
