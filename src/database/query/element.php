<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Database;

/**
 * Query Element Class.
 *
 * @property-read    string  $name      The name of the element.
 * @property-read    array   $elements  An array of elements.
 * @property-read    string  $glue      Glue piece.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 * @since       13.1
 */
class QueryElement
{
	/**
	 * @var    string  The name of the element.
	 * @since  13.1
	 */
	protected $name = null;

	/**
	 * @var    array  An array of elements.
	 * @since  13.1
	 */
	protected $elements = null;

	/**
	 * @var    string  Glue piece.
	 * @since  13.1
	 */
	protected $glue = null;

	/**
	 * Constructor.
	 *
	 * @param   string  $name      The name of the element.
	 * @param   mixed   $elements  String or array.
	 * @param   string  $glue      The glue for elements.
	 *
	 * @since   13.1
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		$this->elements = array();
		$this->name = $name;
		$this->glue = $glue;

		$this->append($elements);
	}

	/**
	 * Magic function to convert the query element to a string.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function __toString()
	{
		if (substr($this->name, -2) == '()')
		{
			return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
		}
		else
		{
			return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
		}
	}

	/**
	 * Appends element parts to the internal list.
	 *
	 * @param   mixed  $elements  String or array.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function append($elements)
	{
		if (is_array($elements))
		{
			$this->elements = array_merge($this->elements, $elements);
		}
		else
		{
			$this->elements = array_merge($this->elements, array($elements));
		}
	}

	/**
	 * Gets the elements of this element.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * Method to provide deep copy support to nested objects and arrays when cloning.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function __clone()
	{
		foreach ($this as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$this->{$k} = unserialize(serialize($v));
			}
		}
	}
}
