<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Registry\Serializer;

use Grisgris\Test\TestCase;
use Grisgris\Registry\SerializerXml;

/**
 * Test case class for Registry XML serializer.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerXmlTest extends TestCase
{
	/**
	 * @var    SerializerXml  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerXml::toString
	 * @since   13.1
	 */
	public function testToString()
	{
		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('nestedarray' => array('test1' => 'value1'));

		$string = "<?xml version=\"1.0\"?>\n<registry>" .
			"<node name=\"foo\" type=\"string\">bar</node>" .
			"<node name=\"quoted\" type=\"string\">\"stringwithquotes\"</node>" .
			"<node name=\"booleantrue\" type=\"boolean\">1</node>" .
			"<node name=\"booleanfalse\" type=\"boolean\"></node>" .
			"<node name=\"numericint\" type=\"integer\">42</node>" .
			"<node name=\"numericfloat\" type=\"double\">3.1415</node>" .
			"<node name=\"section\" type=\"object\">" .
			"<node name=\"key\" type=\"string\">value</node>" .
			"</node>" .
			"<node name=\"array\" type=\"array\">" .
			"<node name=\"nestedarray\" type=\"array\">" .
			"<node name=\"test1\" type=\"string\">value1</node>" .
			"</node>" .
			"</node>" .
			"</registry>\n";

		$this->assertThat(
			$this->_instance->toString($object),
			$this->equalTo($string)
		);
	}

	/**
	 * Tests the `fromString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerXml::fromString
	 * @since   13.1
	 */
	public function testFromString()
	{
		$object = new \stdClass;
		$object->foo = 'bar';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('test1' => 'value1');

		$string = "<?xml version=\"1.0\"?>\n<registry>" .
			"<node name=\"foo\" type=\"string\">bar</node>" .
			"<node name=\"booleantrue\" type=\"boolean\">1</node>" .
			"<node name=\"booleanfalse\" type=\"boolean\"></node>" .
			"<node name=\"numericint\" type=\"integer\">42</node>" .
			"<node name=\"numericfloat\" type=\"double\">3.1415</node>" .
			"<node name=\"section\" type=\"object\">" .
			"<node name=\"key\" type=\"string\">value</node>" .
			"</node>" .
			"<node name=\"array\" type=\"array\">" .
			"<node name=\"test1\" type=\"string\">value1</node>" .
			"</node>" .
			"</registry>\n";

		// Test basic object to string.
		$this->assertThat(
			$this->_instance->fromString($string),
			$this->equalTo($object)
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

		$this->_instance = new SerializerXml;
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
