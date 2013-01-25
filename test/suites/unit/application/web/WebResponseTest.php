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

use Grisgris\Application\WebResponse;
use Grisgris\Provider\Provider;

/**
 * Test case class for WebResponse.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebResponseTest extends TestCase
{
	/**
	 * @var    array  Container for calls to WebResponseRedirect::header().
	 * @since  13.1
	 */
	public $sentHeaders = array();

	/**
	 * @var    WebResponse  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Application.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `appendBody` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::appendBody
	 * @since   13.1
	 */
	public function testAppendBody()
	{
		Reflection::setValue($this->_instance, '_body', array('foo'));

		$this->_instance->appendBody('bar');
		$this->assertEquals(
			array('foo', 'bar'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks the body array has been appended.'
		);

		$this->_instance->appendBody(true);
		$this->assertEquals(
			array('foo', 'bar', '1'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks that non-strings are converted to strings.'
		);
	}

	/**
	 * Tests the `clearHeaders` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::clearHeaders
	 * @since   13.1
	 */
	public function testClearHeaders()
	{
		Reflection::setValue($this->_instance, '_headers', array('foo'));

		$this->_instance->clearHeaders();

		$this->assertEquals(
			array(),
			Reflection::getValue($this->_instance, '_headers'),
			'Checks the headers were cleared.'
		);
	}

	/**
	 * Tests the `getBody` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::getBody
	 * @since   13.1
	 */
	public function testGetBody()
	{
		Reflection::setValue($this->_instance, '_body', array('foo', 'bar'));

		$this->assertEquals(
			'foobar',
			$this->_instance->getBody(),
			'Checks the default state returns the body as a string.'
		);

		$this->assertSame(
			$this->_instance->getBody(),
			$this->_instance->getBody(false),
			'Checks the default state is $asArray = false.'
		);

		$this->assertEquals(
			array('foo', 'bar'),
			$this->_instance->getBody(true),
			'Checks that the body is returned as an array.'
		);
	}

	/**
	 * Tests the `prependBody` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::prependBody
	 * @since   13.1
	 */
	public function testPrependBody()
	{
		Reflection::setValue($this->_instance, '_body', array('foo'));

		$this->_instance->prependBody('bar');

		$this->assertEquals(
			array('bar', 'foo'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks the body array has been prepended.'
		);

		$this->_instance->prependBody(true);

		$this->assertEquals(
			array('1', 'bar', 'foo'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks that non-strings are converted to strings.'
		);
	}

	/**
	 * Tests the `sendHeaders` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::sendHeaders
	 * @since   13.1
	 */
	public function testSendHeaders()
	{
		Reflection::setValue(
			$this->_instance,
			'_headers',
			array(
				array('name' => 'Status', 'value' => 200),
				array('name' => 'X-SendHeaders', 'value' => 'foo')
			)
		);

		// Make sure the response doesn't think headers have already been sent.
		$this->_instance->expects($this->any())
			->method('checkHeadersSent')
			->will($this->returnValue(false));

		$this->sentHeaders = array();

		$this->assertSame(
			$this->_instance,
			Reflection::invoke($this->_instance, 'sendHeaders'),
			'Check chaining.'
		);

		$this->assertEquals(
			array(
				array('Status: 200', null, 200),
				array('X-SendHeaders: foo', true, null),
			),
			$this->sentHeaders
		);
	}

	/**
	 * Tests the `setBody` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::setBody
	 * @since   13.1
	 */
	public function testSetBody()
	{
		Reflection::setValue($this->_instance, '_body', array('foobar'));

		$this->_instance->setBody('foo');

		$this->assertEquals(
			array('foo'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks the body array has been reset.'
		);

		$this->_instance->setBody(true);

		$this->assertEquals(
			array('1'),
			Reflection::getValue($this->_instance, '_body'),
			'Checks reset and that non-strings are converted to strings.'
		);
	}

	/**
	 * Tests the `setHeader` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponse::setHeader
	 * @since   13.1
	 */
	public function testSetHeader()
	{
		Reflection::setValue(
			$this->_instance,
			'_headers',
			array(
				array('name' => 'foo', 'value' => 'bar')
			)
		);

		$this->_instance->setHeader('foo', 'car');

		$this->assertEquals(
			array(
				array('name' => 'foo', 'value' => 'bar'),
				array('name' => 'foo', 'value' => 'car')
			),
			Reflection::getValue($this->_instance, '_headers'),
			'Tests that a header is added.'
		);

		$this->_instance->setHeader('foo', 'car', true);

		$this->assertEquals(
			array(
				array('name' => 'foo', 'value' => 'car')
			),
			Reflection::getValue($this->_instance, '_headers'),
			'Tests that headers of the same name are replaced when requested.'
		);
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
		$this->_instance = $this->getMockBuilder('Grisgris\Application\WebResponse')
			->disableOriginalConstructor()
			->setMethods(array('checkConnectionAlive', 'checkHeadersSent', 'header'))
			->getMock();

		// Make sure we capture all the calls to WebResponse::header().
		$self = $this;
		$this->_instance->expects($this->any())->method('header')->will(
			$this->returnCallback(function($string, $replace = true, $code = null) use($self)
			{
				$self->sentHeaders[] = array($string, $replace, $code);
			})
		);

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
