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
require_once 'JsonManager.php';

/**
 * Description of JsonManagerTest
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class JsonManagerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function createFromJson() {
		$jsonData = '{"id": 11111, "field": "field_name", "value": "new value"}';
		$json = new JsonManager($jsonData);

		$this->assertArrayHasKey('field', $json->get());
	}

	/**
	 * @test
	 */
	public function createNull() {
		$json = new JsonManager();

		$this->assertTrue($json instanceof JsonManager);
	}

	/**
	 * @test
	 */
	public function createFromIncorrectJson() {
		$incorectJsonData = '{"id": 11111, "field": "field_name"; "value": "new value"}';
		$json = new JsonManager($incorectJsonData);
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function getArrayFromNullCreated() {
		$json = new JsonManager();
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function getUnexistsKeyValue() {
		$arData = array('id' => '1111', 'field' => 'field_name', 'value' => 'some value');
		$json = new JsonManager($arData);

		$this->assertTrue(empty($json->get('unknown')));
	}

	/**
	 * @test
	 */
	public function createFromArray() {
		$arData = array('id' => '1111', 'field' => 'field_name', 'value' => 'some value');
		$json = new JsonManager($arData);

		$this->assertArrayHasKey('field', $json->get());
	}

	/**
	 * @test
	 */
	public function createEmptyCollection() {
		$json = new JsonManager(null, true);
		$arr = $json->get();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(empty($arr));
	}

	/**
	 * @test
	 */
	public function addNewRootField() {
		$jsonData = '{"id": 11111, "field": "field_name", "value": "new value"}';
		$json = new JsonManager($jsonData);
		$json->add('addNewRootField', 'new value');
		$this->assertArrayHasKey('addNewRootField', $json->get());
	}

	/**
	 * @test
	 */
	public function addNewNotRootField() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$json->add('fields/new_field', 'new value');

		$this->assertEquals('new value', $json->get('fields')->get('new_field'));
	}

	/**
	 * @test
	 */
	public function getNewJsonData() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$newJson = $json->get('fields');

		$this->assertTrue($newJson instanceof JsonManager);
	}

	/**
	 * @test
	 */
	public function getNotRootField() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$id = $json->get('fields')->get('id');

		$this->assertTrue(is_numeric($id));
	}

	/**
	 * @test
	 */
	public function setNewValueOfExistingKey() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$json->set('fields/value', 'new value of field');

		$this->assertEquals('new value of field', $json->get('fields')->get('value'));
	}

	/**
	 * @test
	 */
	public function getZeroKeyValue() {
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData);
		$this->assertEquals('field name 1', $json->get(0)->get('field'));
	}

	/**
	 * @test
	 */
	public function iterateData() {
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
	public function getAsJsonString() {
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
	public function checkSize() {
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
	public function keyExists() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$this->assertTrue($json->exists('fields'));
	}

	/**
	 * @test
	 */
	public function keyNotExists() {
		$jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
		$json = new JsonManager($jsonData);
		$this->assertFalse($json->exists('field_name'));
	}

	/**
	 * @test
	 */
	public function createCollectionWithIncorrectData() {
		$arData = array(
			array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			array('field' => 'field name 2', 'value' => 'value 2'),
			array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);
		$this->assertEquals(3, $json->length());
		$this->assertTrue(empty($json->get(2)->get('id')));
		$this->assertTrue(!empty($json->get(1)->get('id')));
	}

	/**
	 * @test
	 */
	public function listAllWithNeededKeys() {
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
	public function listAll() {
		$arData = array(
			1 => array('id' => '1', 'field' => 'field name 1', 'value' => 'value 1'),
			2 => array('id' => '2', 'field' => 'field name 2', 'value' => 'value 2'),
			3 => array('id' => '3', 'field' => 'field name 3', 'value' => 'value 3')
		);
		$json = new JsonManager($arData, true);

		$this->assertEquals($arData, $json->listAll());
	}

}
