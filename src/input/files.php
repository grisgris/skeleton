<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Input;

use Grisgris\Provider\Provider;

/**
 * An input class used to manage data from the request files.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class Files extends Input
{
	/**
	 * @var    array  The pivoted data from a $_FILES or compatible array.
	 * @since  13.1
	 */
	protected $decodedData = array();

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  An optional dependency provider.
	 * @param   array     $source    Source data (Optional, default is $_FILES)
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider = null, array $source = null)
	{
		$this->provider = $provider;

		if (is_null($source))
		{
			$this->data = & $_FILES;
		}
		else
		{
			$this->data = & $source;
		}
	}

	/**
	 * Gets a value from the input data.
	 *
	 * @param   string  $name     The name of the input property (usually the name of the files INPUT tag) to get.
	 * @param   mixed   $default  The default value to return if the named property does not exist.
	 * @param   string  $filter   The filter to apply to the file name.
	 *
	 * @return  mixed  The filtered input value.
	 *
	 * @since   13.1
	 */
	public function get($name, $default = null, $filter = '')
	{
		if (isset($this->data[$name]))
		{
			$filter = strtolower($filter);
			$results = $this->decodeData(
				array(
					$this->data[$name]['name'],
					$this->data[$name]['type'],
					$this->data[$name]['tmp_name'],
					$this->data[$name]['error'],
					$this->data[$name]['size']
				),
				$filter
			);

			return $results;
		}

		return $default;

	}

	/**
	 * Method to decode a data array.
	 *
	 * @param   array   $data    The data array to decode.
	 * @param   string  $filter  The filter to apply to the filename.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	protected function decodeData(array $data, $filter = '')
	{
		$result = array();

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = $this->decodeData(array($data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]), $filter);
			}
			return $result;
		}

		// Apply the filter as necessary to file names only.
		$data[0] = $this->filter($data[0], $filter);

		return array('name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]);
	}

	/**
	 * Sets a value.
	 *
	 * @param   string  $name   The name of the input property to set.
	 * @param   mixed   $value  The value to assign to the input property.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function set($name, $value)
	{
	}
}
