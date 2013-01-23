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

use Grisgris\Test\TestCase;
use Grisgris\Input\Files;
use Grisgris\Provider\Provider;

/**
 * Test case class for Files.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class FilesTest extends TestCase
{
	/**
	 * @var    Files  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * @var    Provider  The provider object for constructing the Input.
	 * @since  13.1
	 */
	private $_provider;

	/**
	 * Tests the `get` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Input\Files::get
	 * @since   13.1
	 */
	public function testGet()
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
		$this->_instance = new Files($this->_provider, array());
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
