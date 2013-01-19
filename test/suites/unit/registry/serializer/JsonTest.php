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
use Grisgris\Registry\SerializerJson;

/**
 * Test case class for Registry JSON serializer.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerJsonTest extends TestCase
{
	/**
	 * @var    SerializerJson  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Tests the `toString` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Registry\SerializerJson::toString
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

		$string = '{"foo":"bar","quoted":"\"stringwithquotes\"",' .
			'"booleantrue":true,"booleanfalse":false,' .
			'"numericint":42,"numericfloat":3.1415,' .
			'"section":{"key":"value"},' .
			'"array":{"nestedarray":{"test1":"value1"}}' .
			'}';

		// Test basic object to string.
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
	 * @covers  Grisgris\Registry\SerializerJson::fromString
	 * @since   13.1
	 */
	public function testFromString()
	{
		$string1 = '{"title":"Gris-Gris Skeleton","author":"Me","params":{"show_title":1,"show_abstract":0,"show_author":1,"categories":[1,2]}}';

		$object1 = new \stdClass;
		$object1->title = 'Gris-Gris Skeleton';
		$object1->author = 'Me';
		$object1->params = new \stdClass;
		$object1->params->show_title = 1;
		$object1->params->show_abstract = 0;
		$object1->params->show_author = 1;
		$object1->params->categories = array(1, 2);

		// Test basic JSON string to object.
		$object = $this->_instance->fromString($string1);
		$this->assertThat(
			$object,
			$this->equalTo($object1),
			'Line:' . __LINE__ . ' The complex JSON string should convert into the appropriate object.'
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

		$this->_instance = new SerializerJson;
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
