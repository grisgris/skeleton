<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Uri
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Uri;

use Grisgris\Test\TestCase;
use Grisgris\Uri\Uri;

/**
 * Test case class for Uri.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Uri
 * @since       13.1
 */
class UriTest extends TestCase
{
	/**
	 * @var    Uri  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `__toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::__toString
	 * @since   13.1
	 */
	public function test__toString()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->__toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);
	}

	/**
	 * Test the `parse` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::parse
	 * @since   13.1
	 */
	public function testParse()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value&amp;test=true#fragment');

		$this->assertThat(
			$this->_instance->getHost(),
			$this->equalTo('www.example.com')
		);

		$this->assertThat(
			$this->_instance->getPath(),
			$this->equalTo('/path/file.html')
		);

		$this->assertThat(
			$this->_instance->getScheme(),
			$this->equalTo('http')
		);
	}

	/**
	 * Test the `toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::toString
	 * @since   13.1
	 */
	public function testToString()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);

		$this->_instance->setQuery('somevar=somevalue');
		$this->_instance->setVar('somevar2', 'somevalue2');
		$this->_instance->setScheme('ftp');
		$this->_instance->setUser('root');
		$this->_instance->setPass('secret');
		$this->_instance->setHost('www.example.org');
		$this->_instance->setPort('8888');
		$this->_instance->setFragment('someFragment');
		$this->_instance->setPath('/this/is/a/path/to/a/file');

		$this->assertThat(
			$this->_instance->toString(),
			$this->equalTo('ftp://root:secret@www.example.org:8888/this/is/a/path/to/a/file?somevar=somevalue&somevar2=somevalue2#someFragment')
		);
	}

	/**
	 * Test the `setVar` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setVar
	 * @since   13.1
	 */
	public function testSetVar()
	{
		$this->_instance->setVar('somevar', 'somevalue');

		$this->assertThat(
			$this->_instance->getVar('somevar'),
			$this->equalTo('somevalue')
		);
	}

	/**
	 * Test the `hasVar` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::hasVar
	 * @since   13.1
	 */
	public function testHasVar()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->hasVar('somevar'),
			$this->equalTo(false)
		);

		$this->assertThat(
			$this->_instance->hasVar('var'),
			$this->equalTo(true)
		);
	}

	/**
	 * Test the `getVar` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getVar
	 * @since   13.1
	 */
	public function testGetVar()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getVar('var'),
			$this->equalTo('value')
		);

		$this->assertThat(
			$this->_instance->getVar('var2'),
			$this->equalTo('')
		);

		$this->assertThat(
			$this->_instance->getVar('var2', 'default'),
			$this->equalTo('default')
		);
	}

	/**
	 * Test the `delVar` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::delVar
	 * @since   13.1
	 */
	public function testDelVar()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getVar('var'),
			$this->equalTo('value')
		);

		$this->_instance->delVar('var');

		$this->assertThat(
			$this->_instance->getVar('var'),
			$this->equalTo('')
		);
	}

	/**
	 * Test the `setQuery` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setQuery
	 * @since   13.1
	 */
	public function testSetQuery()
	{
		$this->_instance->setQuery('somevar=somevalue');

		$this->assertThat(
			$this->_instance->getQuery(),
			$this->equalTo('somevar=somevalue')
		);

		$this->_instance->setQuery('somevar=somevalue&amp;test=true');

		$this->assertThat(
			$this->_instance->getQuery(),
			$this->equalTo('somevar=somevalue&test=true')
		);

		$this->_instance->setQuery(array('somevar' => 'somevalue', 'test' => 'true'));

		$this->assertThat(
			$this->_instance->getQuery(),
			$this->equalTo('somevar=somevalue&test=true')
		);
	}

	/**
	 * Test the `getQuery` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getQuery
	 * @since   13.1
	 */
	public function testGetQuery()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getQuery(),
			$this->equalTo('var=value')
		);

		$this->assertThat(
			$this->_instance->getQuery(true),
			$this->equalTo(array('var' => 'value'))
		);
	}

	/**
	 * Test the `buildQuery` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::buildQuery
	 * @since   13.1
	 */
	public function testBuildQuery()
	{
		$params = array(
			'field' => array(
				'price' => array(
					'from' => 5,
					'to' => 10,
				),
				'name' => 'foo'
			),
			'v' => 45);

		$expected = 'field[price][from]=5&field[price][to]=10&field[name]=foo&v=45';
		$this->assertEquals($expected, Uri::buildQuery($params));
	}

	/**
	 * Test the `getScheme` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getScheme
	 * @since   13.1
	 */
	public function testGetScheme()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getScheme(),
			$this->equalTo('http')
		);
	}

	/**
	 * Test the `setScheme` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setScheme
	 * @since   13.1
	 */
	public function testSetScheme()
	{
		$this->_instance->setScheme('ftp');

		$this->assertThat(
			$this->_instance->getScheme(),
			$this->equalTo('ftp')
		);
	}

	/**
	 * Test the `getUser` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getUser
	 * @since   13.1
	 */
	public function testGetUser()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getUser(),
			$this->equalTo('someuser')
		);
	}

	/**
	 * Test the `setUser` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setUser
	 * @since   13.1
	 */
	public function testSetUser()
	{
		$this->_instance->setUser('root');

		$this->assertThat(
			$this->_instance->getUser(),
			$this->equalTo('root')
		);
	}

	/**
	 * Test the `getPass` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getPass
	 * @since   13.1
	 */
	public function testGetPass()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getPass(),
			$this->equalTo('somepass')
		);
	}

	/**
	 * Test the `setPass` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setPass
	 * @since   13.1
	 */
	public function testSetPass()
	{
		$this->_instance->setPass('secret');

		$this->assertThat(
			$this->_instance->getPass(),
			$this->equalTo('secret')
		);
	}

	/**
	 * Test the `getHost` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getHost
	 * @since   13.1
	 */
	public function testGetHost()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getHost(),
			$this->equalTo('www.example.com')
		);
	}

	/**
	 * Test the `setHost` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setHost
	 * @since   13.1
	 */
	public function testSetHost()
	{
		$this->_instance->setHost('www.example.org');

		$this->assertThat(
			$this->_instance->getHost(),
			$this->equalTo('www.example.org')
		);
	}

	/**
	 * Test the `getPort` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getPort
	 * @since   13.1
	 */
	public function testGetPort()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getPort(),
			$this->equalTo('80')
		);
	}

	/**
	 * Test the `setPort` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setPort
	 * @since   13.1
	 */
	public function testSetPort()
	{
		$this->_instance->setPort('8888');

		$this->assertThat(
			$this->_instance->getPort(),
			$this->equalTo('8888')
		);
	}

	/**
	 * Test the `getPath` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getPath
	 * @since   13.1
	 */
	public function testGetPath()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getPath(),
			$this->equalTo('/path/file.html')
		);
	}

	/**
	 * Test the `setPath` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setPath
	 * @since   13.1
	 */
	public function testSetPath()
	{
		$this->_instance->setPath('/this/is/a/path/to/a/file.htm');

		$this->assertThat(
			$this->_instance->getPath(),
			$this->equalTo('/this/is/a/path/to/a/file.htm')
		);
	}

	/**
	 * Test the `getFragment` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::getFragment
	 * @since   13.1
	 */
	public function testGetFragment()
	{
		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->getFragment(),
			$this->equalTo('fragment')
		);
	}

	/**
	 * Test the `setFragment` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::setFragment
	 * @since   13.1
	 */
	public function testSetFragment()
	{
		$this->_instance->setFragment('someFragment');

		$this->assertThat(
			$this->_instance->getFragment(),
			$this->equalTo('someFragment')
		);
	}

	/**
	 * Test the `isSSL` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Uri\Uri::isSSL
	 * @since   13.1
	 */
	public function testIsSSL()
	{
		$this->_instance->parse('https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->isSSL(),
			$this->equalTo(true)
		);

		$this->_instance->parse('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->_instance->isSSL(),
			$this->equalTo(false)
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

		$this->_instance = new Uri;
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
