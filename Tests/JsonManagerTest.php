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
 * @copyright (c) 2015 Beeflow Ltd
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
	public function createFromArray() {
		$arData = array('id' => '1111', 'field' => 'field_name', 'value' => 'some value');
		$json = new JsonManager($arData);

		$this->assertArrayHasKey('field', $json->get());
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

}
