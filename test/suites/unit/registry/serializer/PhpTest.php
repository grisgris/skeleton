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
use Grisgris\Registry\SerializerPhp;

/**
 * Test case class for Registry PHP serializer.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerPhpTest extends TestCase
{
	/**
	 * @var    SerializerPhp  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerPhp::toString
	 * @since   13.1
	 */
	public function testToString()
	{
		$options = array('class' => 'myClass');

		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;

		// The PHP registry format does not support nested objects
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('nestedarray' => array('test1' => 'value1'));

		$string = "<?php\n" .
			"class myClass {\n" .
			"\tpublic \$foo = 'bar';\n" .
			"\tpublic \$quoted = '\"stringwithquotes\"';\n" .
			"\tpublic \$booleantrue = '1';\n" .
			"\tpublic \$booleanfalse = '';\n" .
			"\tpublic \$numericint = '42';\n" .
			"\tpublic \$numericfloat = '3.1415';\n" .
			"\tpublic \$section = array(\"key\" => \"value\");\n" .
			"\tpublic \$array = array(\"nestedarray\" => array(\"test1\" => \"value1\"));\n" .
			"}\n?>";
		$this->assertThat(
			$this->_instance->toString($object, $options),
			$this->equalTo($string)
		);
	}

	/**
	 * Tests the `fromString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerPhp::fromString
	 * @since   13.1
	 */
	public function testFromString()
	{
		$this->assertNotEmpty($this->_instance->fromString(''));
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

		$this->_instance = new SerializerPhp;
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
