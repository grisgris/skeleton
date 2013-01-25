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

use Grisgris\Application\WebResponseRedirect;
use Grisgris\Provider\Provider;
use Grisgris\Application\WebClient;

/**
 * Test case class for WebResponseRedirect.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebResponseRedirectTest extends TestCase
{
	/**
	 * @var    array  Container for calls to WebResponseRedirect::header().
	 * @since  13.1
	 */
	public $sentHeaders = array();

	/**
	 * @var    WebClient  The WebClient object for constructing the object.
	 * @since  13.1
	 */
	private $_client;

	/**
	 * @var    WebResponseRedirect  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the object.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Data for testRedirectWithUrl method.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesSetUrlData()
	{
		return array(
			// Note: url, base, request, (expected result)
			array('/foo', 'http://j.org/', 'http://j.org/index.php?v=11.3', 'http://j.org/foo'),
			array('foo',  'http://j.org/', 'http://j.org/index.php?v=11.3', 'http://j.org/foo'),
		);
	}

	/**
	 * Tests the `getUrl` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponseRedirect::getUrl
	 * @since   13.1
	 */
	public function testGetUrl()
	{
		Reflection::setValue($this->_instance, 'url', 'foobar');

		$this->assertEquals('foobar', $this->_instance->getUrl());
	}

	/**
	 * Tests the `send` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponseRedirect::send
	 * @since   13.1
	 */
	public function testSend()
	{
		$url = 'http://grisgr.is/index.php';

		// Make sure the response doesn't think headers have already been sent.
		$this->_instance->expects($this->any())
			->method('checkHeadersSent')
			->will($this->returnValue(false));

		Reflection::setValue($this->_client, 'engine', WebClient::GECKO);
		Reflection::setValue($this->_instance, 'url', $url);

		$this->sentHeaders = array();

		$this->assertEmpty(
			$this->_instance->send(),
			'Nothing should be set to the body.'
		);

		$this->assertEquals(
			array(
				array('Status: 301 Moved Permanently', null, 301),
				array('Content-Type: application/json; charset=utf-8', true, null),
				array('Location: ' . $url, true, null),
			),
			$this->sentHeaders
		);
	}

	/**
	 * Tests the `send` method with headers already sent.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponseRedirect::send
	 * @since   13.1
	 */
	public function testSendWithHeadersSent()
	{
		$url = 'http://grisgr.is/index.php';

		// Make sure the response thinks headers have already been sent.
		$this->_instance->expects($this->any())
			->method('checkHeadersSent')
			->will($this->returnValue(true));

		Reflection::setValue($this->_instance, 'url', $url);

		// Capture the output for this test.
		ob_start();
		$this->_instance->send();
		$buffer = ob_get_contents();
		ob_end_clean();

		$this->assertThat(
			$buffer,
			$this->equalTo("<script>document.location.href='{$url}';</script>\n")
		);
	}

	/**
	 * Tests the `send` method with the JavaScript output.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebResponseRedirect::send
	 * @since   13.1
	 */
	public function testSendWithJavascriptRedirect()
	{
		$url = 'http://grisgr.is/index.php?phi=Φ';

		// Make sure the response doesn't think headers have already been sent.
		$this->_instance->expects($this->any())
			->method('checkHeadersSent')
			->will($this->returnValue(false));

		// Inject the client information.
		Reflection::setValue($this->_client, 'engine', WebClient::TRIDENT);
		Reflection::setValue($this->_instance, 'url', $url);

		// Capture the output for this test.
		ob_start();
		$this->_instance->send();
		$buffer = ob_get_contents();
		ob_end_clean();

		$this->assertThat(
			trim($buffer),
			$this->equalTo(
				'<html><head>'
					. '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'
					. "<script>document.location.href='{$url}';</script>"
					. '</head><body></body></html>'
			)
		);
	}

	/**
	 * Tests the `setUrl` method with assorted URLs.
	 *
	 * @param   string  $url       The URL string to set.
	 * @param   string  $base      The application base URI to use.
	 * @param   string  $request   The application request URI to use.
	 * @param   string  $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebResponseRedirect::setUrl
	 * @dataProvider  casesSetUrlData
	 * @since         13.1
	 */
	public function testSetUrl($url, $base, $request, $expected)
	{
		Reflection::setValue($this->_client, 'engine', WebClient::GECKO);

		$this->_provider->set('uri.base.full', $base);
		$this->_provider->set('uri.request', $request);

		$this->assertSame(
			$this->_instance,
			$this->_instance->setUrl($url),
			'Check that method chaining works.'
		);

		$this->assertEquals(
			$expected,
			Reflection::getValue($this->_instance, 'url')
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
		$this->_client = new WebClient;
		$this->_provider->set('web.client', $this->_client);

		$this->_instance = $this->getMockBuilder('Grisgris\Application\WebResponseRedirect')
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
