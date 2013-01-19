<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

use Grisgris\Event\Event;

/**
 * A listener to use for the Dispatcher tests.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class FooListener
{
	public function onBeforeSomething(Event $e)
	{
		$foo = $e->getArgument('foo');
		$foo[] = 1;
		$e->setArgument('foo', $foo);
	}

	public function onSomething(Event $e)
	{

	}

	public function onAfterSomething(Event $e)
	{

	}
}
