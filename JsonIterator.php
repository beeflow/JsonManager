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

/**
 * Description of JsonIterator
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class JsonIterator implements \Iterator {

	/**
	 * @var Collection
	 */
	private $collection;

	/**
	 * @var int
	 */
	private $currentIndex = 0;

	/**
	 * @var array
	 */
	private $keys;

	/**
	 *
	 * @param Collection $collection
	 */
	public function __construct(JsonManager $collection) {
		$this->collection = $collection;
		$this->keys = $this->collection->keys();
	}

	/**
	 * @return object
	 * @throws Exception
	 */
	public function current() {
		return $this->collection->get($this->keys[$this->currentIndex]);
	}

	/**
	 * @return mixed
	 */
	public function key() {
		return $this->keys[$this->currentIndex];
	}

	/**
	 *
	 */
	public function next() {
		$this->currentIndex++;
	}

	/**
	 *
	 */
	public function rewind() {
		$this->currentIndex = 0;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return (isset($this->keys[$this->currentIndex]));
	}

}
