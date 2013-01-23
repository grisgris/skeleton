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

use Grisgris\Loader;
use Grisgris\Input\Input;
use Grisgris\Provider\Provider;

Loader::register('Grisgris\Test\Suites\Unit\Input\InvalidFilter', __DIR__ . '/stubs/invalidfilter.php');
Loader::register('Grisgris\Test\Suites\Unit\Input\ValidFilter', __DIR__ . '/stubs/validfilter.php');

/**
 * Test case class for Input.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class InputTest extends TestCase
{
	/**
	 * @var    Input  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Input.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `__call` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::__call
	 * @since   13.1
	 */
	public function test__call()
	{
		$data['foo'] = '42g';
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertSame(
			$this->_instance->getInteger('foo'),
			$this->_instance->getInt('foo')
		);

		$data['foo'] = '1';
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertSame(
			$this->_instance->getBoolean('foo'),
			$this->_instance->getBool('foo')
		);

		$data['foo'] = '3.14!!';
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertSame(
			$this->_instance->getFloat('foo'),
			$this->_instance->getDouble('foo')
		);
	}

	/**
	 * Tests the `__construct` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::__construct
	 * @since   13.1
	 */
	public function test__construct()
	{
		$this->assertSame($this->_provider, Reflection::getValue($this->_instance, 'provider'));

		$input = new Input($this->_provider);
		$this->assertSame($_REQUEST, Reflection::getValue($this->_instance, 'data'));
	}

	/**
	 * Tests the `__get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::__get
	 * @since   13.1
	 */
	public function test__get()
	{
		$_POST['foo'] = 'bar';
		$_POST['method'] = 'post';
		$_GET['foo'] = 'baz';
		$_GET['method'] = 'get';

		$post = $this->_instance->__get('post');
		$this->assertSame($_POST, Reflection::getValue($post, 'data'));

		$get = $this->_instance->__get('get');
		$this->assertSame($_GET, Reflection::getValue($get, 'data'));

		$post = $this->_instance->__get('post');
		$this->assertSame($_POST, Reflection::getValue($post, 'data'));

		$this->assertInstanceOf('Grisgris\Input\Cookie', $this->_instance->__get('cookie'));
	}

	/**
	 * Tests the `__get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::__get
	 * @since   13.1
	 */
	public function test__getWithInvalidType()
	{
		$this->setExpectedException('UnexpectedValueException');
		$this->_instance->__get('gooberflasm');
	}

	/**
	 * Tests the `count` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::count
	 * @since   13.1
	 */
	public function testCount()
	{
		$this->assertEquals(
			count($_REQUEST),
			count($this->_instance)
		);

		$this->assertEquals(
			count($_POST),
			count($this->_instance->post)
		);

		$this->assertEquals(
			count($_GET),
			count($this->_instance->get)
		);
	}

	/**
	 * Tests the `get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::get
	 * @since   13.1
	 */
	public function testGet()
	{
		$data['foo'] = 'bar';
		Reflection::setValue($this->_instance, 'data', $data);

		// Test the get method.
		$this->assertThat(
			$this->_instance->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		$_GET['foo'] = 'bar2';

		// Test the get method.
		$this->assertThat(
			$this->_instance->get->get('foo'),
			$this->equalTo('bar2'),
			'Line: ' . __LINE__ . '.'
		);

		// Test the get method.
		$this->assertThat(
			$this->_instance->get('default_value', 'default'),
			$this->equalTo('default'),
			'Line: ' . __LINE__ . '.'
		);

	}

	/**
	 * Tests the `def` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::def
	 * @since   13.1
	 */
	public function testDef()
	{
		$data['foo'] = 'bar';
		Reflection::setValue($this->_instance, 'data', $data);

		$this->_instance->def('foo', 'nope');
		$tmp = Reflection::getValue($this->_instance, 'data');
		$this->assertEquals('bar', $tmp['foo']);

		$this->_instance->def('Ping', 'Pong');
		$tmp = Reflection::getValue($this->_instance, 'data');
		$this->assertEquals('Pong', $tmp['Ping']);
	}

	/**
	 * Tests the `getArray` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::getArray
	 * @covers  Grisgris\Input\Input::filterArray
	 * @since   13.1
	 */
	public function testGetArray()
	{
		$data = array(
			'var1' => 'value1?',
			'var2' => '34',
			'var3' => array('test')
		);
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertEquals(
			array(
				'var1' => 'value1',
				'var2' => 34,
				'var3' => array('test')
			),
			$this->_instance->getArray(
				array(
					'var1' => 'alphanumeric',
					'var2' => 'integer',
					'var3' => ''
				)
			)
		);
	}

	/**
	 * Tests the `getArray` method using a nested filter set.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::getArray
	 * @covers  Grisgris\Input\Input::filterArray
	 * @since   13.1
	 */
	public function testGetArrayNested()
	{
		$data = array(
			'var2' => 34,
			'var3' => array('var2' => 'test '),
			'var4' => array('var1' => array('var3' => 'test'))
		);
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertEquals(
			array(
				'var2' => 34,
				'var3' => array('var2' => 'test')
			),
			$this->_instance->getArray(
				array(
					'var2' => 'integer',
					'var3' => array('var2' => 'word')
				)
			)
		);

		$this->assertEquals(
			array(
				'var4' => array('var1' => array('var2' => ''))
			),
			$this->_instance->getArray(
				array(
					'var4' => array('var1' => array('var2' => 'string'))
				)
			)
		);
	}

	/**
	 * Tests the `getArray` method without specifying variables.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::getArray
	 * @since   13.1
	 */
	public function testGetArrayWithoutSpecifiedVariables()
	{
		$data = array(
			'var2' => 34,
			'var3' => array('var2' => 'test'),
			'var4' => array('var1' => array('var2' => 'test')),
			'var5' => array('foo' => array()),
			'var6' => array('bar' => null),
			'var7' => null
		);
		Reflection::setValue($this->_instance, 'data', $data);

		$this->assertEquals($this->_instance->getArray(), $data);
	}

	/**
	 * Tests the `getMethod` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::getMethod
	 * @since   13.1
	 */
	public function testGetMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'PATCH';
		$this->assertEquals('PATCH', $this->_instance->getMethod());

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertEquals('POST', $this->_instance->getMethod());

		$_SERVER['REQUEST_METHOD'] = 'get';
		$this->assertEquals('GET', $this->_instance->getMethod());
	}

	/**
	 * Tests the `set` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::set
	 * @since   13.1
	 */
	public function testSet()
	{
		$data['foo'] = 'bar2';
		Reflection::setValue($this->_instance, 'data', $data);

		$this->_instance->set('foo', 'bar');
		$tmp = Reflection::getValue($this->_instance, 'data');
		$this->assertEquals('bar', $tmp['foo']);
	}

	/**
	 * Tests the `filter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filter
	 * @since   13.1
	 */
	public function testFilter()
	{
		$this->assertEquals(
			'abc123!@#',
			Reflection::invoke($this->_instance, 'filter', 'abc123!@#', '')
		);
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filter', 'abc123!@#', 'alphanumeric')
		);
	}

	/**
	 * Tests the `filter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filter
	 * @since   13.1
	 */
	public function testFilterWithInvalidFilterName()
	{
		$this->setExpectedException('InvalidArgumentException');
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filter', 'abc123!@#', 'foobar')
		);
	}

	/**
	 * Tests the `filter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filter
	 * @since   13.1
	 */
	public function testFilterWithInvalidFilterObject()
	{
		$this->_provider->set('filter.foobar', new InvalidFilter);

		$this->setExpectedException('InvalidArgumentException');
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filter', 'abc123!@#', 'foobar')
		);
	}

	/**
	 * Tests the `filter` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filter
	 * @since   13.1
	 */
	public function testFilterWithValidFilterObject()
	{
		$this->_provider->set('filter.foobar', new ValidFilter);
		$this->assertEquals(
			42,
			Reflection::invoke($this->_instance, 'filter', 'abc123!@#', 'foobar')
		);
	}

	/**
	 * Tests the `filterAlphanumeric` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterAlphanumeric
	 * @since   13.1
	 */
	public function testFilterAlphanumeric()
	{
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filterAlphanumeric', 'abc123')
		);
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filterAlphanumeric', 'abc123!@#')
		);
	}

	/**
	 * Tests the `filterBase64` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterBase64
	 * @since   13.1
	 */
	public function testFilterBase64()
	{
		$this->assertEquals(
			'SSBwdXQgYSBzcGVsbCBvbiB5b3Uu',
			Reflection::invoke($this->_instance, 'filterBase64', 'SSBwdXQgYSBzcGVsbCBvbiB5b3Uu')
		);
		$this->assertEquals(
			'SSBwdXQgYSBzcGVsbCBvbiB5b3Uu',
			Reflection::invoke($this->_instance, 'filterBase64', 'SSBwdXQgYSBzcGVsbCBvbiB5b3Uu@#')
		);
	}

	/**
	 * Tests the `filterBoolean` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterBoolean
	 * @since   13.1
	 */
	public function testFilterBoolean()
	{
		$this->assertEquals(
			true,
			Reflection::invoke($this->_instance, 'filterBoolean', 'true')
		);
		$this->assertEquals(
			true,
			Reflection::invoke($this->_instance, 'filterBoolean', '1')
		);
		$this->assertEquals(
			false,
			Reflection::invoke($this->_instance, 'filterBoolean', '0')
		);
		$this->assertEquals(
			false,
			Reflection::invoke($this->_instance, 'filterBoolean', 'false')
		);
		$this->assertEquals(
			false,
			Reflection::invoke($this->_instance, 'filterBoolean', null)
		);
	}

	/**
	 * Tests the `filterCommand` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterCommand
	 * @since   13.1
	 */
	public function testFilterCommand()
	{
		$this->assertEquals(
			'abc.123',
			Reflection::invoke($this->_instance, 'filterCommand', 'abc.123')
		);
		$this->assertEquals(
			'abc123',
			Reflection::invoke($this->_instance, 'filterCommand', 'abc123!@#')
		);
	}

	/**
	 * Tests the `filterEmail` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterEmail
	 * @since   13.1
	 */
	public function testFilterEmail()
	{
		$this->assertEquals(
			'noreply@grisgr.is',
			Reflection::invoke($this->_instance, 'filterEmail', 'noreply@grisgr.is')
		);
		$this->assertEquals(
			'noreply+blah@grisgr.is',
			Reflection::invoke($this->_instance, 'filterEmail', 'noreply+blah@grisgr.is')
		);
	}

	/**
	 * Tests the `filterFloat` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterFloat
	 * @since   13.1
	 */
	public function testFilterFloat()
	{
		$this->assertEquals(
			3.14,
			Reflection::invoke($this->_instance, 'filterFloat', '3.14')
		);
		$this->assertEquals(
			3.14,
			Reflection::invoke($this->_instance, 'filterFloat', '  3.14')
		);
		$this->assertEquals(
			0.0,
			Reflection::invoke($this->_instance, 'filterFloat', false)
		);
	}

	/**
	 * Tests the `filterInteger` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterInteger
	 * @since   13.1
	 */
	public function testFilterInteger()
	{
		$this->assertEquals(
			42,
			Reflection::invoke($this->_instance, 'filterInteger', '42')
		);
		$this->assertEquals(
			42,
			Reflection::invoke($this->_instance, 'filterInteger', '42g')
		);
		$this->assertEquals(
			0,
			Reflection::invoke($this->_instance, 'filterInteger', false)
		);
	}

	/**
	 * Tests the `filterPath` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterPath
	 * @since   13.1
	 */
	public function testFilterPath()
	{
		$this->assertEquals(
			'usr/local/grisgris',
			Reflection::invoke($this->_instance, 'filterPath', 'usr/local/grisgris')
		);
		$this->assertEquals(
			'',
			Reflection::invoke($this->_instance, 'filterPath', '/usr/local/grisgris')
		);
		$this->assertEquals(
			'',
			Reflection::invoke($this->_instance, 'filterPath', '../../../../etc/passwd')
		);
	}

	/**
	 * Tests the `filterString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterString
	 * @since   13.1
	 */
	public function testFilterString()
	{
		$this->assertEquals(
			'Hello World!',
			Reflection::invoke($this->_instance, 'filterString', '<p>Hello World!</p>')
		);
		$this->assertEquals(
			')(*FWHIj0fw93fj()W#J)J',
			Reflection::invoke($this->_instance, 'filterString', ')(*FWHIj0fw93fj()W#J)J')
		);
	}

	/**
	 * Tests the `filterUrl` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterUrl
	 * @since   13.1
	 */
	public function testFilterUrl()
	{
		$this->assertEquals(
			'http://user:pass@domain.com:port/path/to/resource.ext?query=param&foo=bar#fragment',
			Reflection::invoke($this->_instance, 'filterUrl', 'http://user:pass@domain.com:port/path/to/resource.ext?query=param&foo=bar#fragment')
		);
		$this->assertEquals(
			'abc123!@#',
			Reflection::invoke($this->_instance, 'filterUrl', 'abc123!@#')
		);
	}

	/**
	 * Tests the `filterWord` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::filterWord
	 * @since   13.1
	 */
	public function testFilterWord()
	{
		$this->assertEquals(
			'abc',
			Reflection::invoke($this->_instance, 'filterWord', 'abc.123')
		);
		$this->assertEquals(
			'word',
			Reflection::invoke($this->_instance, 'filterWord', 'word')
		);
	}

	/**
	 * Tests the `loadGlobalInputs` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Input::loadGlobalInputs
	 * @since   13.1
	 */
	public function testLoadGlobalInputs()
	{
		$this->markTestIncomplete();
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
		$this->_instance = new Input($this->_provider, array());
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
		Reflection::setValue($this->_instance, 'loaded', false);
		$this->_instance = null;
		$this->_provider = null;

		parent::tearDown();
	}
}
