# JsonManager

JsonMaganer helps to manage json data as an object.

With this class you can:
- add new field to selected path
- set new value of field in selected path
- get value of field
- get new JsonManager object if value of field is array
- use JsonManager in foreach loop


### Installation
#### composer

```
composer require beeflow/jsonmanager
```
    
#### GIT
Just run

    git clone https://github.com/beeflow/JsonManager.git
    
### Example
#### Simple Json
```php
$jsonData = '{"id": 11111, "field": "field_name", "value": "new value"}';
$json = new JsonManager($jsonData);
$json->add('addNewRootField', 'new value');
$given = $json->get();
```

#### Array collection
```php
$arData = [
    ['id' => '1', 'field' => 'field name 1', 'value' => 'value 1'],
    ['id' => '2', 'field' => 'field name 2', 'value' => 'value 2'],
    ['id' => '3', 'field' => 'field name 3', 'value' => 'value 3']
];
$json = new JsonManager($arData, true);
```

#### List fields

```php
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
```
