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
use Beeflow\JsonManager\Tests\Mock\SomeClass;
use PHPUnit\Framework\TestCase;

/**
 * Description of JsonManagerTest
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class JsonManagerTest extends TestCase
{

    /**
     * @test
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function getUnexistsKeyValue()
    {
        $arData = ['id' => '1111', 'field' => 'field_name', 'value' => 'some value'];
        $json = new JsonManager($arData);
        $given = $json->get('unknown');

        $this->assertTrue(empty($given));
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createFromArray()
    {
        $arData = ['id' => '1111', 'field' => 'field_name', 'value' => 'some value'];
        $json = new JsonManager($arData);
        $given = $json->get();

        $this->assertArrayHasKey('field', $given);
    }

    /**
     * @test
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function getZeroKeyValue()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
        $json = new JsonManager($arData);
        $given = $json->get(0)->get('field');

        $this->assertEquals('field name 1', $given);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function iterateData()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
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
     * @throws \ReflectionException
     */
    public function getAsJsonString()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
        $json = new JsonManager($arData);
        $expected = '[{"id":"1","field":"field name 1","value":"value 1"},{"id":"2","field":"field name 2","value":"value 2"},{"id":"3","field":"field name 3","value":"value 3"}]';
        $given = (string) $json;

        $this->assertEquals($expected, $given);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function checkSize()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
        $json = new JsonManager($arData);
        $this->assertEquals(3, $json->length());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function keyExists()
    {
        $jsonData = '{"fields": {"id": 11111, "field": "field_name", "value": "new value"}}';
        $json = new JsonManager($jsonData);
        $this->assertTrue($json->exists('fields'));
    }

    /**
     * @test
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function createCollectionWithIncorrectData()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
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
     * @throws \ReflectionException
     */
    public function listAllWithNeededKeys()
    {
        $arData = [
            ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
        $json = new JsonManager($arData, true);
        $expected = [
            1 => ['field' => 'field name 1'],
            2 => ['field' => 'field name 2'],
            3 => ['field' => 'field name 3']
        ];
        $this->assertEquals($expected, $json->listAll(['field']));
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function listAll()
    {
        $arData = [
            1 => ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
            2 => ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
            3 => ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
        ];
        $json = new JsonManager($arData, true);

        $this->assertEquals($arData, $json->listAll());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function parseObject()
    {
        $someObject = new SomeClass();
        $json = new JsonManager($someObject);

        $this->assertEquals('four', $json->get('four'));
    }
}
