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

use Grisgris\Date\Date;
use Grisgris\Provider\Provider;
use Grisgris\Service\Json;

/**
 * Test case class for Json.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 * @since       13.1
 */
class JsonTest extends TestCase
{
	/**
	 * @var    Json  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the object.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `_toArray` method with invalid response type.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::_toArray
	 * @since   13.1
	 */
	public function test_toArray()
	{
		$date = new Date;
		$input = (object) array('foo' => (object) array('bar' => 'baz', 'self' => (object) array('a' => 42, 'b' => $date)));

		$this->assertEquals(
			array(
				'foo' => array(
					'bar' => 'baz',
					'self' => array(
						'a' => 42,
						'b' => $date
					)
				)
			),
			Reflection::invoke($this->_instance, '_toArray', $input)
		);
	}

	/**
	 * Tests the `createResponse` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::createResponse
	 * @since   13.1
	 */
	public function testCreateResponse()
	{
		$response = Reflection::invoke($this->_instance, 'createResponse', 'OK');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
		$this->assertAttributeEquals('application/json', 'contentType', $response);

		$response = Reflection::invoke($this->_instance, 'createResponse', 'ok');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
		$this->assertAttributeEquals('application/json', 'contentType', $response);

		$response = Reflection::invoke($this->_instance, 'createResponse', 'internalServerError');
		$this->assertInstanceOf('Grisgris\Application\WebResponseInternalServerError', $response);
		$this->assertAttributeEquals('application/json', 'contentType', $response);
	}

	/**
	 * Tests the `createResponse` method with invalid response type.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::createResponse
	 * @since   13.1
	 */
	public function testCreateResponseWithInvalidResponseType()
	{
		$this->setExpectedException('InvalidArgumentException');

		$response = Reflection::invoke($this->_instance, 'createResponse', 'foobar');
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
	}

	/**
	 * Tests the `processBody` method with invalid response type.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::processBody
	 * @since   13.1
	 */
	public function testProcessBody()
	{
		$date = new Date;
		$input = (object) array('foo' => (object) array('bar' => 'baz', 'self' => (object) array('a' => 42, 'b' => $date)));

		$this->assertEquals(
			array(
				'foo' => array(
					'bar' => 'baz',
					'self' => array(
						'a' => 42,
						'b' => $date->toUnix() * 1000
					)
				)
			),
			Reflection::invoke($this->_instance, 'processBody', $input)
		);
	}

	/**
	 * Tests the `setResponse` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::setResponse
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

		$this->assertEquals('"foobar"', $response->getBody());
		$this->assertEquals(array(array('name' => 'foo', 'value' => 'bar')), $response->getHeaders());
		$this->assertInstanceOf('Grisgris\Application\WebResponseOk', $response);
	}

	/**
	 * Tests the `setResponse` method with complex object.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Service\Json::setResponse
	 * @since   13.1
	 */
	public function testSetResponseWithComplexObject()
	{
		$application = $this->getMockBuilder('Grisgris\Application\Web')
		->disableOriginalConstructor()
		->setMethods(array('doExecute', 'fetchConfigurationData'))
		->getMock();

		Reflection::setValue($this->_instance, 'application', $application);

		$body = array('foo' => 'bar', 'bar' => array(42, 'baz'));

		Reflection::invoke($this->_instance, 'setResponse', $body, 'created');
		$response = Reflection::getValue($application, 'response');

		$this->assertEquals('{"foo":"bar","bar":[42,"baz"]}', $response->getBody());
		$this->assertInstanceOf('Grisgris\Application\WebResponseCreated', $response);
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
		$this->_instance = $this->getMockBuilder('Grisgris\Service\Json')
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
