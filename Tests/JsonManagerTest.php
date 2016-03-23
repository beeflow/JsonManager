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
namespace Beeflow\JsonManager\Tests;
use Beeflow\JsonManager\JsonManager;

/**
 * Description of JsonManagerTest
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class JsonManagerTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 */
	public function createFromJson()
	{
		$jsonData = '{"id": 11111, "field": "field_name", "value": "new value"}';
		$json = new JsonManager($jsonData);
		$given = $json->get();

		$this->assertArrayHasKey('field', $given);
	}

	/**
	 * @test
	 */
	public function createNull()
	{
		$json = new JsonManager();

		$this->assertTrue($json instanceof JsonManager);
	}

	/**
	 * @test
	 */
	public function createFromIncorrectJson()
	{
		$incorectJsonData = '{"id": 11111, "field": "field_name"; "value": "new value"}';
		$json = new JsonManager($incorectJsonData);
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function getArrayFromNullCreated()
	{
		$json = new JsonManager();
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function getUnexistsKeyValue()
	{
		$arData = array('id' => '1111', 'field' => 'field_name', 'value' => 'some value');
		$json = new JsonManager($arData);
		$given = $json->get('unknown');

		$this->assertTrue(empty($given));
	}

	/**
	 * @test
	 */
	public function createFromArray()
	{
		$arData = array('id' => '1111', 'field' => 'field_name', 'value' => 'some value');
		$json = new JsonManager($arData);
		$given = $json->get();

		$this->assertArrayHasKey('field', $given);
	}

	/**
	 * @test
	 */
	public function createEmptyCollection()
	{
		$json = new JsonManager(null, true);
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function addNewRootField()
	{
		$jsonData = '{"id": 11111, "field": "field_name", "value": "new value"}';
		$json = new JsonManager($jsonData);
		$json->add('addNewRootField', 'new value');
		$given = $json->get();

		$this->assertArrayHasKey('addNewRootField', $given);
	}

	/**
	 * @test
	 */
	public function addNewNotRootField()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$json->add('fields/new_field', 'new value');

		$this->assertEquals('new value', $json->get('fields')->get('new_field'));
	}

	/**
	 * @test
	 */
	public function getNewJsonData()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$newJson = $json->get('fields');

		$this->assertTrue($newJson instanceof JsonManager);
	}

	/**
	 * @test
	 */
	public function getNotRootField()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$id = $json->get('fields')->get('id');

		$this->assertTrue(is_numeric($id));
	}

	/**
	 * @test
	 */
	public function setNewValueOfExistingKey()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$json->set('fields/value', 'new value of field');
		$given = $json->get('fields')->get('value');

		$this->assertEquals('new value of field', $given);
	}

	/**
	 * @test
	 */
	public function getZeroKeyValue()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData);
		$given = $json->get(0)->get('field');

		$this->assertEquals('field name 1', $given);
	}

	/**
	 * @test
	 */
	public function iterateData()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);
		$i = 1;
		foreach ($json as $value) {
			$this->assertEquals($i, $value->get('id'));
			$this->assertEquals("field name $i", $value->get('field'));
			$this->assertEquals("value $i", $value->get('value'));
			$i++;
		}
	}

	/**
	 * @test
	 */
	public function getAsJsonString()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData);
		$expected = '[{"id":"1","field":"field name 1","value":"value 1"},{"id":"2","field":"field name 2","value":"value 2"},{"id":"3","field":"field name 3","value":"value 3"}]';
		$given = (string) $json;

		$this->assertEquals($expected, $given);
	}

	/**
	 * @test
	 */
	public function checkSize()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData);
		$this->assertEquals(3, $json->length());
	}

	/**
	 * @test
	 */
	public function keyExists()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$this->assertTrue($json->exists('fields'));
	}

	/**
	 * @test
	 */
	public function keyNotExists()
	{
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$given = $json->exists('field_name');

		$this->assertFalse($given);
	}

	/**
	 * @test
	 */
	public function createCollectionWithIncorrectData()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);
		$jsonLength = $json->length();
		$secondId = $json->get(2)->get('id');
		$firstId = $json->get(1)->get('id');

		$this->assertEquals(3, $jsonLength);
		$this->assertTrue(empty($secondId));
		$this->assertTrue(!empty($firstId));
	}

	/**
	 * @test
	 */
	public function listAllWithNeededKeys()
	{
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);
		$expected = array(
			1 => array('field' => 'field name 1'),
			2 => array('field' => 'field name 2'),
			3 => array('field' => 'field name 3')
		);
		$this->assertEquals($expected, $json->listAll(array('field')));
	}

	/**
	 * @test
	 */
	public function listAll()
	{
		$arData = array(
			1 => array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			2 => array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			3 => array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);

		$this->assertEquals($arData, $json->listAll());
	}

}
