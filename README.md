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

Add to your composer.json

    "require": {
          ...
          "beeflow/JsonManager": "dev-master",
      },
      ...
      "repositories": [
          {
              "type": "git",
              "url": "https://github.com/beeflow/JsonManager.git"
          }
      ]

and then run

    composer update
    
#### GIT
Just run

    git clone https://github.com/beeflow/JsonManager.git
    
