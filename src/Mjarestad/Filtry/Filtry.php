<?php

namespace Mjarestad\Filtry;

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
|
| Filter and sanitize input data in Laravel 4 or as a standalone package.
|
*/
class Filtry
{
	/**
	 * Input data
	 * @var array
	 */
	protected $data = array();

	/**
	 * Filtered data
	 * @var array
	 */
	protected $filteredData = array();

	/**
	 * Filters to apply to data
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Filter extensions
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * Filter recursive on input array
	 * @var boolean
	 */
	protected $recursive;

	/**
	 * Set input data and filters
	 * @param  array $data
	 * @param  string|array $filters
	 * @param boolean $recursive
	 * @return Filtry
	 */
	public function make($data, $filters, $recursive = true)
	{
		$this->data 		= $data;
		$this->filters 		= $this->explodeFilters($filters);
		$this->recursive 	= $recursive;

		return $this;
	}

	/**
	 * Get filtered data
	 * @return array
	 */
	public function getFiltered()
	{
		$this->filter();

        return array_merge($this->data, $this->filteredData);
	}

	/**
	 * Get unfiltered data
	 * @return array
	 */
	public function getOld()
	{
		return $this->data;
	}

	/**
	 * Extend with custom filters
	 * @param  string $filter
	 * @param  $extension
	 * @return void
	 */
	public function extend($filter, $extension)
	{
		$this->extensions[$filter] = $extension;
	}

	/**
	 * Trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function trim($data, $charlist = ' ')
	{
		return trim($data, $charlist);
	}

	/**
	 * Left trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function ltrim($data, $charlist = ' ')
	{
		return ltrim($data, $charlist);
	}

	/**
	 * Right trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function rtrim($data, $charlist = ' ')
	{
		return rtrim($data, $charlist);
	}

	/**
	 * String to lower
	 * @param  string $data
	 * @return string
	 */
	public function lower($data)
	{
		return strtolower($data);
	}

	/**
	 * String to upper
	 * @param  string $data
	 * @return string
	 */
	public function upper($data)
	{
		return strtoupper($data);
	}

	/**
	 * UC first
	 * @param  string $data
	 * @return string
	 */
	public function ucfirst($data)
	{
		return ucfirst($data);
	}

	/**
	 * UC words
	 * @param  string $data
	 * @return string
	 */
	public function ucwords($data)
	{
		return ucwords($data);
	}

	/**
	 * Strip slashes
	 * @param  string $data
	 * @return string
	 */
	public function stripslashes($data)
	{
		return stripslashes($data);
	}

	/**
	 * Snake case
	 * @param  string $data
     * @param string $delimiter
	 * @return string
	 */
	public function snakeCase($value, $delimiter = '_')
	{
		$replace = '$1'.$delimiter.'$2';

		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
	}

	/**
	 * Camel case
	 * @param  string $data
	 * @return string
	 */
	public function camelCase($data)
	{
		return lcfirst($this->studlyCase($data));
	}

	/**
	 * Studly case
	 * @param  string $data
	 * @return string
	 */
	public function studlyCase($data)
	{
		$data = ucwords(str_replace(array('-', '_'), ' ', $data));

		return str_replace(' ', '', $data);
	}

	/**
	 * XSS clean with htmlspecialchars
	 * @param  string $data
	 * @return string
	 */
	public function xssClean($data)
	{
		return htmlspecialchars($data);
	}

	/**
	 * Remove whitespace
	 * @param  string $data
	 * @return string
	 */
	public function removeWhitespace($data)
	{
		return $this->stripWhitespaces($data);
	}

    /**
     * Strip all white spaces
     * @param $data
     * @return mixed
     */
    public function stripWhitespaces($data)
    {
        return str_replace(' ', '', $data);
    }

    /**
     * Strip all dashes
     * @param $data
     * @return mixed
     */
    public function stripDashes($data)
    {
        return str_replace('-', '', $data);
    }

	/**
	 * Remove all special chars and make it url-friendly
	 * @param  string $data
	 * @param  string $separator
	 * @return string
	 */
	public function slug($data, $separator = '-')
	{
		$data = trim(iconv( 'UTF-8', 'ASCII//TRANSLIT', $data ));
		$data = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $data );
		$data = strtolower( trim( $data, '-' ) );
		$data = preg_replace( "/[\/_|+ -]+/", $separator, $data );

		return $data;
	}

	/**
	 * Add http or https if missing
	 * @param  string  $url
	 * @return string
	 */
	public function prepUrl($url)
	{
        if (empty($url)) {
            return '';
        }

        return parse_url($url, PHP_URL_SCHEME) === null ? 'http://' . $url : $url;
	}

	/**
	 * Dynamically call extension filters
	 * @param  string $filter
	 * @param  array $data
	 * @return mixed
     * @throws \Exception
	 */
	public function __call($filter, $data)
	{
		$filter = $this->snakeCase($filter);

		if (isset($this->extensions[$filter])) {
			return call_user_func($this->extensions[$filter], $data[0]);
		}

		throw new \Exception("Filter extension '$filter' does not exist.");
	}

	/**
	 * Run provided filters on data
	 * @return array
     * @throws \Exception
	 */
	protected function filter()
	{
		foreach ($this->filters as $attribute => $filters) {
			// Check if the attribute is present in the input data
			if (!array_key_exists($attribute, $this->data)) {
				continue;
			}

			$data = $this->data[$attribute];

			foreach ($filters as $filter) {
				if (method_exists($this, $this->camelCase($filter))) {
					$data = $this->filterWalker($filter, $data);
				} elseif (isset($this->extensions[$filter])) {
					$data = $this->filterExtensionWalker($filter, $data);
				} else {
					throw new \Exception("'$filter' is not a valid filter.");
				}
			}

			$this->filteredData[$attribute] = $data;
		}

		return $this->filteredData;
	}

	/**
	 * Walk through filters
	 * @param  string $filter
	 * @param  array $data
	 * @return string|array
	 */
	protected function filterWalker($filter, $data)
	{
		$filter = $this->camelCase($filter);

		if (is_array($data) and $this->recursive === true) {
			foreach ($data as $key => $value) {
				$data[$key] = call_user_func(array($this, $filter), $value);
			}
		} else {
			$data = call_user_func(array($this, $filter), $data);
		}

		return $data;
	}

	/**
	 * Walk through extension filters
	 * @param  string $filter
	 * @param  array $data
	 * @return string|array
	 */
	protected function filterExtensionWalker($filter, $data)
	{
		if (is_array($data) and $this->recursive === true) {
			foreach ($data as $key => $value) {
				$data[$key] = call_user_func($this->extensions[$filter], $data);
			}
		} else {
			$data = call_user_func($this->extensions[$filter], $data);
		}

		return $data;
	}

	/**
	 * Explode the filters into an array of filters.
	 * @param  string|array  $filters
	 * @return array
	 */
	protected function explodeFilters($filters)
	{
		foreach ($filters as $key => &$filter) {
			$filter = (is_string($filter)) ? explode('|', $filter) : $filter;
		}

		return $filters;
	}
}
