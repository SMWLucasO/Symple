# Symple
A Symple PHP Framework for CRUD web applications.

### Getting started
It's very simple to get started with Symple, take a look at the example and make sure match all the prerequisites!

### Prerequisites
Symple has a few prerequisites. They are as follows
* A minimal version of PHP 7
* A database which has an information_schema (innodb for example)

That is all, the framework was written in raw PHP!

# Examples
Before doing anything at all, you should require the autoloader.
<br>This file can be found at the root of the project.
```php
require_once 'Symple/autoloader.php';
```
This is necessary for usage of any object which we use.

## Abstract Database Model
The Abstract database model is built up using 4 useful, but necessary<br>
classes, they form the foundation of Symple.
* Model
* Entity
* Extending the entity
* Registering the entity

This system represents a database CRUD system.

There will be an explanation of each, and some examples.

### Model
A model represents the table for any query you will be making, you can select the table for this model by using the following
```php
    $model = Symple\database\adm\Model::get('TABLENAME');
```
Using this, we have a bunch of methods we can use.

### ▶ byId
Argument(s): 'id'

Description: Selects a row from the database table, the id is from the primary key. This works if you use an id as primary key.

Usage:
```php
    $model = Symple\database\adm\Model::get('TABLENAME');
    $entity = $model->byId(5);
```

Returns: an Entity object linked to this 'id' or null.

### ▶ by
Argument(s): 'column', 'value'<br>
Optional argument(s): 'filter'

Description: Select one or multiple rows from the database table, where the specified column's value is equal to the specified one.

Usage:
```php
    $model = Symple\database\adm\Model::get('TABLENAME');
    $entity = $model->by('column', 'value');
```

Returns: array of 'Entity' object or Entity object depending on the amount of rows retrieved.

### ▶ all
Argument(s): None.

Description: Select all the rows in the database table, put them into an array and return it.

Usage:
```php
    $model = Symple\database\adm\Model::get('TABLENAME');
    $entity = $model->by('column', 'value');
```

Returns: array of 'Entity' object
### ▶ create
Argument(s): 'values', 'verification'
Optional argument(s): 'filter'

Description: Insert a new row into the model's related table, takes 2 associative arrays, the first being an associative array of column => value and the second being column => type, where type is 'int', 'bool', 'string', 'double' or 'ignore'

Usage:
```php
    $model = Symple\database\adm\Model::get('TABLENAME');
    $entity = $model->create(
        array(
            'COLUMN_1' => 'nVar',
            'COLUMN_2' => 'anotherVar'
        ),
        array(
            'COLUMN_1' => 'string',
            'COLUMN_2' => 'string'
        )
```
### Entity
The Entity object is the object representation of the row selected through the Model. We can use this to update, delete and read.
To make this happen, we have an amount of functions, and a way to add your own functionalities through inheritance. But you'll need to link this through the configuration.

### ▶ Read
Reading data from an entity is very simple, we use the name of the columns and use these as variables, as such.
```php
    // Get the table, and retrieve the row with the id '1'
    $entity = Symple\database\adm\Model::get('TABLENAME')->byId(1);
    
    // in this case, the column names are 'description' and 'name'
    echo $entity->description;
    echo $entity->name;
```

It is that simple.

### ▶ Update
The 'Entity' object has a special functionality created for updating the row it represents. There's multiple ways to do so, too.

Method 1: Enabling auto update.
```php

    // Get an entity from the specified table with an id of '1'
    $entity = Symple\database\adm\Model::get('TABLENAME')->byId(1);
    
    $entity->setAutoUpdate(true); // Now you can update the variables from the database AND the object itself at once.
    
    // 'description' column of the row gets updated to new description
    $entity->description = 'new description';

```
[warning]: Do not forget to disable auto update once you're done updating. It could have disastrous after effects (accidents happen!)

Method 2: Using the update functionality
```php
    // Get an entity from the specified table with an id of '1'
    $entity = Symple\database\adm\Model::get('TABLENAME')->byId(1);
    
    // now that we have the entity, we can update columns as such
    $entity->update(
        array(
            'description' => 'New description'
        ),
        array(
            'description' => 'string'
        ) // possible to do an optional filter after this argument, see filter class.
    );
    
```

These functionalities both have their own pros and cons, choose wisely!

### ▶ Delete

Deleting a row which is represented by the Entity object is very simple.
```php

    // Get an entity from the specified table with an id of '1'
    $entity = Symple\database\adm\Model::get('TABLENAME')->byId(1);
    
    // delete the row related to the entity.
    $entity->delete();

```

That's it, nothing more.

### Extending the entity

There is naturally cases where you wish to add methods to a specific Entity(specific tables), don't worry! We have got your back!
We use inheritance to achieve this, and we use it like this

```php

class myClass extends Symple\database\adm\Entity {
    // add functionalities here.
}

```

After this, all you need to do is add your functionalities to it and register the entity!
Every other predefined functionality will still apply to the extended class.

### Registering the entity

Registering your entity to the configuration file is very simple, in Symple/config/config.php
you can see an array, and there's another array inside called 'defined_entites'.<br>
You can register it by adding a new associative key => value pair to the array as such

```php

    // works with namespaces too, of course!
    'defined_entities' => [
        'database_table' => myClass::class
        'database_table_2' a\namespace\myClass::class
    ]
    
```

That's it, you're already done!

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
