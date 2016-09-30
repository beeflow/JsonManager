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
namespace Beeflow\JsonManager;

use Beeflow\JsonManager\JsonIterator;

/**
 * Description of JsonManager
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class JsonManager implements \IteratorAggregate
{

    /**
     *
     * @var array
     */
    private $item = array();

    /**
     * JsonManager constructor.
     *
     * @param Mixed|null $inJsonString
     * @param bool       $collection
     * @param string     $keyName
     */
    public function __construct($inJsonString = null, $collection = false, $keyName = 'id')
    {
        if (is_string($inJsonString)) {
            $jsonString = str_replace('\"', '"', $inJsonString);
        } else if (is_object($inJsonString)) {
            $jsonString = $this->parseObject($inJsonString);
        } else {
            $jsonString = $inJsonString;
        }

        if (isset($jsonString) && !is_array($jsonString)) {
            $items = json_decode($jsonString, true);
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
     * @param SomeObject $inObject
     *
     * @return array
     */
    private function parseObject($inObject)
    {
        $reflection = new \ReflectionClass(get_class($inObject));
        $properties = $reflection->getProperties();
        $arData = array();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $key = $property->getName();
            $arData[ $key ] = $property->getValue($inObject);
        }

        return $arData;
    }

    /**
     * @param String $path with value name
     * @param Mixed  $value
     */
    public function set($path, $value)
    {
        $pathElements = explode('/', $path);
        $array = &$this->item;
        foreach ($pathElements as $key) {
            $array = &$array[ $key ];
        }
        $array = $value;
        unset($array);
    }

    /**
     * @param String $path with value name
     * @param Mixed  $value
     */
    public function add($path, $value)
    {
        $this->set($path, $value);
    }

    /**
     *
     * @param String $fieldName
     *
     * @return Mixed
     */
    public function get($fieldName = null)
    {
        if (!isset($fieldName) || $fieldName === false) {
            return $this->item;
        }
        if (isset($this->item[ $fieldName ]) && is_array($this->item[ $fieldName ])) {
            return new JsonManager($this->item[ $fieldName ]);
        }
        if (isset($this->item[ $fieldName ])) {
            return $this->item[ $fieldName ];
        }

        return NULL;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->item);
    }

    /**
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->item);
    }

    /**
     *
     * @return integer
     */
    public function length()
    {
        return sizeof($this->item);
    }

    /**
     *
     * @param array() $neededKeys
     *
     * @return array()
     */
    public function listAll($neededKeys = array())
    {
        $results = array();
        foreach ($this->item as $name => $object) {
            foreach ($object as $key => $value) {
                if (empty($neededKeys) || in_array($key, $neededKeys)) {
                    $results[ $name ][ $key ] = $value;
                }
            }
        }

        return $results;
    }

    /**
     *
     * @param string /integer $key
     *
     * @return boolean
     */
    public function exists($key)
    {
        return (isset($this->item[ $key ]));
    }

    /**
     *
     * @return Jsoniterator
     */
    public function getIterator()
    {
        return new JsonIterator($this);
    }

    /**
     * @param $items
     * @param $keyName
     */
    private function makeCollection($items, $keyName)
    {
        if (empty($items)) {
            $this->item = array();

            return;
        }
        foreach ($items as $item) {
            if (!empty($keyName) && array_key_exists($keyName, $item)) {
                $this->item[ $item[ $keyName ] ] = new JsonManager($item);
            } else {
                $this->item[] = new JsonManager($item);
            }
        }
    }

}
