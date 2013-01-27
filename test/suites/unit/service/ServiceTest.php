<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Service;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Provider\Provider;
use Grisgris\Service\Service;

/**
 * Test case class for Service.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 * @since       13.1
 */
class ServiceTest extends TestCase
{
	/**
	 * @var    Service  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the object.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `clearHeaders` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Service::clearHeaders
	 * @since   13.1
	 */
	public function testClearHeaders()
	{
		Reflection::setValue($this->_instance, '_headers', array('foo', 'bar'));

		$this->assertAttributeNotEmpty('_headers', $this->_instance);

		$this->assertSame(
			$this->_instance,
			Reflection::invoke($this->_instance, 'clearHeaders'),
			'Check method chaining.'
		);

		$this->assertAttributeEmpty('_headers', $this->_instance);
	}

	/**
	 * Tests the `createResponse` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Service::createResponse
	 * @since   13.1
	 */
	public function testCreateResponse()
	{
		$response = Reflection::invoke($this->_instance, 'createResponse', 'OK');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);

		$response = Reflection::invoke($this->_instance, 'createResponse', 'ok');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);

		$response = Reflection::invoke($this->_instance, 'createResponse', 'internalServerError');
		$this->assertInstanceOf('Grisgris\Application\WebResponseInternalServerError', $response);
	}

	/**
	 * Tests the `createResponse` method with invalid response type.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Service::createResponse
	 * @since   13.1
	 */
	public function testCreateResponseWithInvalidResponseType()
	{
		$this->setExpectedException('InvalidArgumentException');

		$response = Reflection::invoke($this->_instance, 'createResponse', 'foobar');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
	}

	/**
	 * Tests the `setHeader` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Service::setHeader
	 * @since   13.1
	 */
	public function testSetHeader()
	{
		Reflection::setValue($this->_instance, '_headers', array());

		$this->assertAttributeEmpty('_headers', $this->_instance);

		$this->assertSame(
			$this->_instance,
			Reflection::invoke($this->_instance, 'setHeader', 'foo', 'bar', false),
			'Check method chaining.'
		);

		$this->assertAttributeEquals(
			array(
				array('name' => 'foo', 'value' => 'bar', 'replace' => false)
			),
			'_headers',
			$this->_instance
		);
	}

	/**
	 * Tests the `setResponse` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Service::setResponse
	 * @since   13.1
	 */
	public function testSetResponse()
	{
		$application = $this->getMockBuilder('Grisgris\Application\Web')
			->disableOriginalConstructor()
			->setMethods(array('doExecute', 'fetchConfigurationData'))
			->getMock();

		Reflection::setValue($this->_instance, 'application', $application);

		Reflection::invoke($this->_instance, 'setHeader', 'foo', 'bar', false);
		Reflection::invoke($this->_instance, 'setResponse', 'foobar', 'ok');
		$response = Reflection::getValue($application, 'response');

		$this->assertEquals('foobar', $response->getBody());
		$this->assertEquals(array(array('name' => 'foo', 'value' => 'bar')), $response->getHeaders());
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
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
		$this->_instance = $this->getMockBuilder('Grisgris\Service\Service')
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
