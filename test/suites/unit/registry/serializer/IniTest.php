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
use Grisgris\Registry\SerializerIni;

/**
 * Test case class for Registry INI serializer.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerIniTest extends TestCase
{
	/**
	 * @var    SerializerIni  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerIni::toString
	 * @since   13.1
	 */
	public function testToString()
	{
		$object = new \stdClass;
		$object->foo = 'bar';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';

		// Test basic object to string.
		$this->assertThat(
			trim($this->_instance->toString($object)),
			$this->equalTo("foo=\"bar\"\nbooleantrue=true\nbooleanfalse=false\nnumericint=42\nnumericfloat=3.1415\n\n[section]\nkey=\"value\"")
		);
	}

	/**
	 * Tests the `fromString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerIni::fromString
	 * @since   13.1
	 */
	public function testFromString()
	{
		$string2 = "[section]\nfoo=bar";

		$object1 = new \stdClass;
		$object1->foo = 'bar';

		$object2 = new \stdClass;
		$object2->section = $object1;

		// Test INI format string without sections.
		$this->assertThat(
			$this->_instance->fromString($string2, array('processSections' => false)),
			$this->equalTo($object1)
		);

		// Test INI format string with sections.
		$this->assertThat(
			$this->_instance->fromString($string2, array('processSections' => true)),
			$this->equalTo($object2)
		);

		// Test empty string
		$this->assertThat(
			$this->_instance->fromString(null),
			$this->equalTo(new \stdClass)
		);

		$string4 = "boolfalse=false\nbooltrue=true\nkeywithoutvalue\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";
		$object3 = new \stdClass;
		$object3->boolfalse = false;
		$object3->booltrue = true;
		$object3->numericfloat = 3.1415;
		$object3->numericint = 42;
		$object3->key = 'value';

		$this->assertThat(
			$this->_instance->fromString($string4),
			$this->equalTo($object3)
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

		$this->_instance = new SerializerIni;
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
