<?php

/**
 * GNU General Public License (Version 2, June 1991)
 *
 * This program is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 */
require_once 'JsonIterator.php';

/**
 * Description of JsonManager
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright (c) 2015 Beeflow Ltd
 */
class JsonManager implements \IteratorAggregate {

	/**
	 *
	 * @var array 
	 */
	private $item;

	/**
	 * @param String $jsonString may be NULL
	 */
	public function __construct($inJsonString = null, $collection = false, $keyName = 'id') {
		$jsonString = str_replace('\"', '"', $inJsonString);
		if (isset($jsonString) && !is_array($jsonString)) {
			// http://stackoverflow.com/questions/11267769/is-there-a-php-5-3-bug-concerning-json-decode-returning-null-on-valid-json-strin
			$items = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $jsonString), true);
		} else if (!empty($jsonString) && is_array($jsonString)) {
			$items = $jsonString;
		}
		if (empty($items)) {
			$items = array();
		}

		if ($collection) {
			$this->makeCollection($items, $keyName);
		} else {
			$this->item = $items;
		}
	}

	/**
	 * @param String $path with value name
	 * @param Mixed $value
	 */
	public function set($path, $value) {
		$pathElements = explode('/', $path);
		$array = &$this->item;
		foreach ($pathElements as $key) {
			$array = &$array[$key];
		}
		$array = $value;
		unset($array);
	}

	/**
	 * @param String $path with value name
	 * @param Mixed $value
	 */
	public function add($path, $value) {
		$this->set($path, $value);
	}

	/**
	 * 
	 * @param String $fieldName
	 * @return Mixed
	 */
	public function get($fieldName = null) {
		if (!isset($fieldName) || $fieldName === false) {
			return $this->item;
		}
		if (isset($this->item[$fieldName]) && is_array($this->item[$fieldName])) {
			return new JsonManager($this->item[$fieldName]);
		}
		if (isset($this->item[$fieldName])) {
			return $this->item[$fieldName];
		}
		return NULL;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return json_encode($this->item);
	}

	/**
	 *
	 * @return array
	 */
	public function keys() {
		return array_keys($this->item);
	}

	/**
	 *
	 * @return integer
	 */
	public function length() {
		return sizeof($this->item);
	}

	/**
	 * 
	 * @param array() $neededKeys
	 * @return array()
	 */
	public function listAll($neededKeys = array()) {
		$results = array();
		foreach ($this->item as $name => $object) {
			foreach ($object as $key => $value) {
				if (empty($neededKeys) || in_array($key, $neededKeys)) {
					$results[$name][$key] = $value;
				}
			}
		}
		return $results;
	}

	/**
	 *
	 * @param string/integer $key
	 * @return boolean
	 */
	public function exists($key) {
		return (isset($this->item[$key]));
	}

	/**
	 * 
	 * @return Jsoniterator
	 */
	public function getIterator() {
		return new JsonIterator($this);
	}

	private function makeCollection($items, $keyName) {
		if (empty($items)) {
			$this->item = array();
			return;
		}
		foreach ($items as $item) {
			if (!empty($keyName) && array_key_exists($keyName, $item)) {
				$this->item[$item[$keyName]] = new JsonManager($item);
			} else {
				$this->item[] = new JsonManager($item);
			}
		}
	}

}
