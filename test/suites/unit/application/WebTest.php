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

use Grisgris\Application\Web;
use Grisgris\Provider\Provider;
use Grisgris\Registry\Registry;

$_SERVER['HTTP_HOST'] = 'mydomain.com';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
$_SERVER['REQUEST_URI'] = '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

/**
 * Test case class for Web.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebTest extends TestCase
{
	/**
	 * @var    Web  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Application.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Data for detectRequestUri method.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesDetectRequestUriData()
	{
		return array(
			//   HTTPS, PHP_SELF,       REQUEST_URI,            HTTP_HOST,     SCRIPT_NAME,    QUERY_STRING, (resulting uri)
			array(null, '/g/index.php', '/g/index.php?foo=bar', 'grisgr.is:3', '/g/index.php', '',        'http://grisgr.is:3/g/index.php?foo=bar'),
			array('on', '/g/index.php', '/g/index.php?foo=bar', 'grisgr.is:3', '/g/index.php', '',        'https://grisgr.is:3/g/index.php?foo=bar'),
			array(null, '',             '',                     'grisgr.is:3', '/g/index.php', '',        'http://grisgr.is:3/g/index.php'),
			array(null, '',             '',                     'grisgr.is:3', '/g/index.php', 'foo=bar', 'http://grisgr.is:3/g/index.php?foo=bar'),
		);
	}

	/**
	 * Tests the `__construct` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::__construct
	 * @since   13.1
	 */
	public function test__constructWithPrimedProvider()
	{
		$client = $this->getMock('Grisgris\Application\WebClient');

		$provider = new Provider;
		$provider->set('web.client', $client);

		$this->_instance->__construct($provider);

		$this->assertSame(
			$client,
			Reflection::getValue($this->_instance, 'client')
		);
	}

	/**
	 * Tests the `__construct` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::__construct
	 * @since   13.1
	 */
	public function test__constructWithEmptyProvider()
	{
		$this->_instance->__construct();

		$this->assertInstanceOf(
			'Grisgris\Application\WebClient',
			Reflection::getValue($this->_instance, 'client')
		);
	}

	/**
	 * Tests the JApplicationWeb::detectRequestUri method.
	 *
	 * @param   string  $https        The value to set for $_SERVER['HTTPS'].
	 * @param   string  $phpSelf      The value to set for $_SERVER['PHP_SELF'].
	 * @param   string  $requestUri   The value to set for $_SERVER['REQUEST_URI'].
	 * @param   string  $httpHost     The value to set for $_SERVER['HTTP_HOST'].
	 * @param   string  $scriptName   The value to set for $_SERVER['SCRIPT_NAME'].
	 * @param   string  $queryString  The value to set for $_SERVER['QUERY_STRING'].
	 * @param   string  $expects      The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  casesDetectRequestUriData
	 * @since         13.1
	 */
	public function testDetectRequestUri($https, $phpSelf, $requestUri, $httpHost, $scriptName, $queryString, $expects)
	{
		$_SERVER['HTTPS'] = $https;
		$_SERVER['PHP_SELF'] = $phpSelf;
		$_SERVER['REQUEST_URI'] = $requestUri;
		$_SERVER['HTTP_HOST'] = $httpHost;
		$_SERVER['SCRIPT_NAME'] = $scriptName;
		$_SERVER['QUERY_STRING'] = $queryString;

		$this->assertEquals($expects, Reflection::invoke($this->_instance, 'detectRequestUri'));
	}

	/**
	 * Tests the `loadSystemUris` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::loadSystemUris
	 * @since   13.1
	 */
	public function testLoadSystemUrisWithSiteUriSet()
	{
		// Set the site.uri value in the configuration.
		$config = new Registry(array('site' => array('uri' => 'http://test.grisgr.is/path/')));
		Reflection::setValue($this->_instance, 'config', $config);

		Reflection::invoke($this->_instance, 'loadSystemUris');

		$this->assertEquals(
			'http://test.grisgr.is/path/',
			$this->_provider->get('uri.base.full'),
			'Checks the full base uri.'
		);

		$this->assertEquals(
			'http://test.grisgr.is',
			$this->_provider->get('uri.base.host'),
			'Checks the base uri host.'
		);

		$this->assertEquals(
			'/path/',
			$this->_provider->get('uri.base.path'),
			'Checks the base uri path.'
		);

		$this->assertEquals(
			'http://test.grisgr.is/path/media/',
			$this->_provider->get('uri.media.full'),
			'Checks the full media uri.'
		);

		$this->assertEquals(
			'/path/media/',
			$this->_provider->get('uri.media.path'),
			'Checks the media uri path.'
		);
	}

	/**
	 * Tests the `loadSystemUris` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::loadSystemUris
	 * @since   13.1
	 */
	public function testLoadSystemUrisWithoutSiteUriSet()
	{
		$config = new Registry;
		Reflection::setValue($this->_instance, 'config', $config);
		Reflection::invoke($this->_instance, 'loadSystemUris', 'http://grisgr.is/application');

		$this->assertEquals(
			'http://grisgr.is/',
			$this->_provider->get('uri.base.full'),
			'Checks the full base uri.'
		);

		$this->assertEquals(
			'http://grisgr.is',
			$this->_provider->get('uri.base.host'),
			'Checks the base uri host.'
		);

		$this->assertEquals(
			'/',
			$this->_provider->get('uri.base.path'),
			'Checks the base uri path.'
		);

		$this->assertEquals(
			'http://grisgr.is/media/',
			$this->_provider->get('uri.media.full'),
			'Checks the full media uri.'
		);

		$this->assertEquals(
			'/media/',
			$this->_provider->get('uri.media.path'),
			'Checks the media uri path.'
		);
	}

	/**
	 * Tests the `loadSystemUris` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::loadSystemUris
	 * @since   13.1
	 */
	public function testLoadSystemUrisWithoutSiteUriWithMediaUriSet()
	{
		// Set the media_uri value in the configuration.
		$config = new Registry(array('media' => array('uri' => 'http://cdn.grisgr.is/media/')));
		Reflection::setValue($this->_instance, 'config', $config);

		Reflection::invoke($this->_instance, 'loadSystemUris', 'http://grisgr.is/application');

		$this->assertEquals(
			'http://grisgr.is/',
			$this->_provider->get('uri.base.full'),
			'Checks the full base uri.'
		);

		$this->assertEquals(
			'http://grisgr.is',
			$this->_provider->get('uri.base.host'),
			'Checks the base uri host.'
		);

		$this->assertEquals(
			'/',
			$this->_provider->get('uri.base.path'),
			'Checks the base uri path.'
		);

		$this->assertEquals(
			'http://cdn.grisgr.is/media/',
			$this->_provider->get('uri.media.full'),
			'Checks the full media uri.'
		);

		// Since this is on a different domain we need the full url for this too.
		$this->assertEquals(
			'http://cdn.grisgr.is/media/',
			$this->_provider->get('uri.media.path'),
			'Checks the media uri path.'
		);
	}

	/**
	 * Tests the `loadSystemUris` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::loadSystemUris
	 * @since   13.1
	 */
	public function testLoadSystemUrisWithoutSiteUriWithRelativeMediaUriSet()
	{
		// Set the media_uri value in the configuration.
		$config = new Registry(array('media' => array('uri' => '/media/')));
		Reflection::setValue($this->_instance, 'config', $config);

		Reflection::invoke($this->_instance, 'loadSystemUris', 'http://grisgr.is/application');

		$this->assertEquals(
			'http://grisgr.is/',
			$this->_provider->get('uri.base.full'),
			'Checks the full base uri.'
		);

		$this->assertEquals(
			'http://grisgr.is',
			$this->_provider->get('uri.base.host'),
			'Checks the base uri host.'
		);

		$this->assertEquals(
			'/',
			$this->_provider->get('uri.base.path'),
			'Checks the base uri path.'
		);

		$this->assertEquals(
			'http://grisgr.is/media/',
			$this->_provider->get('uri.media.full'),
			'Checks the full media uri.'
		);

		$this->assertEquals(
			'/media/',
			$this->_provider->get('uri.media.path'),
			'Checks the media uri path.'
		);
	}

	/**
	 * Tests the `isSSLConnection` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::isSSLConnection
	 * @since   13.1
	 */
	public function testIsSSLConnection()
	{
		unset($_SERVER['HTTPS']);
		$this->assertEquals(false, $this->_instance->isSSLConnection());

		$_SERVER['HTTPS'] = 'on';
		$this->assertEquals(true, $this->_instance->isSSLConnection());
	}

	/**
	 * Tests the `fetchClient` method with a primed provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::fetchClient
	 * @since   13.1
	 */
	public function testFetchClientWithPrimedProvider()
	{
		$mock = $this->getMock('Grisgris\Application\WebClient');
		$this->_provider->set('web.client', $mock);

		$this->assertSame(
			$mock,
			Reflection::invoke($this->_instance, 'fetchClient')
		);
	}

	/**
	 * Tests the `fetchClient` method with an empty provider.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\Web::fetchClient
	 * @since   13.1
	 */
	public function testFetchClientWithEmptyProvider()
	{
		$mock = $this->getMock('Grisgris\Application\WebClient');
		$this->_provider->set('web.client', null);

		$actual = Reflection::invoke($this->_instance, 'fetchClient');
		$this->assertInstanceOf('Grisgris\Application\WebClient', $actual);
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
		$this->_instance = $this->getMockForAbstractClass('Grisgris\Application\Web', array(), '', false);
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
