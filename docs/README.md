# Component class

> `Phproberto\Joomla\Component\Component`

Component class is intended to ease the management of component related stuff.  

Things like retrieve and change parameters are made easy.

* [Methods](#methods)
    * [admin()](#admin)
    * [clear($option)](#clear)
    * [clearAll()](#clearAll)
    * [get($option)](#get)
    * [getActive()](#getActive)
    * [getClient()](#getClient)
    * [getExtension()](#getExtension)
    * [getExtensionProperty($property, $default = null)](#getExtensionProperty)
    * [getFresh($option)](#getFresh)
    * [getModel($name, array $config = array('ignore_request' => true))](#getModel)
    * [getPrefix()](#getPrefix)
    * [getTable($name, array $config = array())](#getTable)
    * [saveParams()](#saveParams)
    * [site()](#site)

## Methods<a id="methods"></a>

### admin() <a id="admin"></a>

> Switches the component client to admin to search for models, tables, etc. 

**Parameters:**

None

**Returns:**

`self` Self instance for chaining

**Examples:**

```php
// This will search for a model in backend
Component::get('com_content')
    ->admin()
    ->getModel('Articles');

```

### clear($option) <a id="clear"></a>

> Clears a cached instance from the static cache. 

By default components are statically cached to prevent their information being retrieved multiple times from the database in the same time execution. This is perfect for 99% of uses but there are special cases where you may want to ensure that you clear the cached instance. 

**Parameters:**

* *$option (required):* Component option. Example: com_content.

**Returns:**

`void`

**Examples:**

```php
// This will store a param in the statically cached instance
Component::get('com_content')
    ->setParam('foo', 'var');

// This will return `var` because instance is cached
$foo = Component::get('com_content')
    ->getParam('foo');

// This will clear the cached instance
Component::clear('com_content');

// This will return `NULL` because cached instance was cleared
$foo = Component::get('com_content')
    ->getParam('foo');
```

### clearAll() <a id="clearAll"></a>

> Clears all the cached instances from the static cache. 

**Parameters:**

None

**Returns:**

`void`

**Examples:**

```php
// This will store a param in the statically cached instance
Component::get('com_content')
    ->setParam('foo', 'var');

Component::get('com_menus')
    ->setParam('foo2', 'var22');

// This will clear all the cached instances
Component::clearAll();

// This will return `NULL` because cached instance was cleared
$foo = Component::get('com_content')
    ->getParam('foo');

// This will return `NULL` because cached instance was cleared
$foo = Component::get('com_menus')
    ->getParam('foo2')
```

### get($option)<a id="get"></a>

> Retrieve an instance of a specific component.

It will return a statically cached instance if the component has been already loaded or a fresh one if not.

**Parameters:**

* *$option (required)*: Component option. Example: com_content.

**Returns:**

`Phproberto\Joomla\Component\Component`;

**Examples:**

```php
// Retrieve com_content component
$component = Component::get('com_content');

if ($component->getParam('show_title', '1') === '1')
{
    // Do something
}
```

### getActive() <a id="getActive"></a>

> Try to load the active component.

This will retrieve the active component based on url option. 

**Parameters:**

None

**Returns:**

`Phproberto\Joomla\Component\Component`;

**Examples:**

```php
// Load active component parameters
try
{
    $component = Component::getActive();
}
catch (\InvalidArgumentException $e)
{
    $component = null;
}

return $component ? $component->getParams() : new Registry;
```

### getClient() <a id="getClient"></a>

> Get the active client in the component.

**Parameters:**

None

**Returns:**

`Phproberto\Joomla\Client\Client`;

**Examples:**

```php
// If not specified component will use active client
$component = Component::get('com_content');

if ($component->getClient()->isSite())
{
    // Do something
}
```

### getExtension() <a id="getExtension"></a>

> Retrieves the component extension information from the #__extensions table. 

**Parameters:**

None

**Returns:**

`\stdClass` Object with the extension info

**Examples:**

```php
// Retrieve the full extension
$extension = Component::get('com_content')->getExtension();

// Access extension properties. getExtension is cached so it won't execute multiple queries
$extensionId = Component::get('com_content')->getExtension()->extension_id;
```

### getExtensionProperty($property, $default = null) <a id="getExtensionProperty"></a>

> Retrieve a specific value of the associated extension.

This allows you to access a component extensions' property specifying a default value.

**Parameters:**

* *$property (required)*: Name of the property to retrieve.
* *$default (optional)*: Default value to use if property is null

**Returns:**

`mixed`

**Examples:**

```php
// Folder of this extension
echo Component::get('com_content')->getExtensionProperty('folder', 'None');
```

### getFresh($option) <a id="getFresh"></a>

> Retrieve a non-statically-cached instance.

By default components are statically cached to prevent their information being retrieved multiple times from the database in the same time execution. This is perfect for 99% of uses but there are special cases where you may want to ensure that you clear the cached instance. 


**Parameters:**

* *$option (required)*: Component option. Example: com_content.

**Returns:**

`Phproberto\Joomla\Component\Component`;

**Examples:**

```php
// This will store a param in the statically cached instance
Component::get('com_content')
    ->setParam('foo', 'var');

// This will return `var` because instance is cached
$foo = Component::get('com_content')
    ->getParam('foo');

// This will return `NULL` because cached instance was cleared
$foo = Component::getFresh('com_content')
    ->getParam('foo');
```

### getModel($name, array $config = array('ignore_request' => true)) <a id="getModel"></a>

> Retrieve component's model.

Try to find and load a model of the component.

**Parameters:**

* *$name (required)*: Name of the model to load. Example: Menu.
* *$config (optional)*: Custom configuration for the model

**Returns:**

`string`

**Examples:**

```php
// Retrieve a backend model
$model = Component::get('com_banners')->admin()->getModel('Banner');
$model = Component::get('com_content')->admin()->getModel('Articles');

// Retrieve a frontend model
$model = Component::get('com_users')->site()->getTable('Registration');
```

### getPrefix() <a id="getPrefix"></a>

> Retrieve component's classes prefix.

Guesses the prefix used for a component based on its option. Example: `Content` for `com_content`, `Complex_Prefix` for `com_complex_prefix`.

**Parameters:**

None

**Returns:**

`string`

**Examples:**

```php
// Retrieve com_content component
$component = Component::get('com_content');

// Returns Content
$prefix = $component->getPrefix();
```

### getTable($name, array $config = array()) <a id="getTable"></a>

> Retrieve component's table.

Try to find and load a table of the component.

**Parameters:**

* *$name (required)*: Name of the table to load. Example: Menu.
* *$config (optional)*: Custom configuration for the table

**Returns:**

`string`

**Examples:**

```php
$table = Component::get('com_banners')->admin()->getTable('Banner');
$table = Component::get('com_banners')->admin()->getTable('Client');
$table = Component::get('com_menus')->admin()->getTable('Menu');
```

### saveParams() <a id="saveParams"></a>

> Save component's parameters in the database.

Save current instance parameters into the database.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$component = Component::get('com_banners');
$component->setParam('purchase_type', 2);

if ($component->saveParams())
{
    echo 'Correctly saved!';
}
```

### site() <a id="site"></a>

> Switches the component client to site to search for models, tables, etc. 

**Parameters:**

None

**Returns:**

`self` Self instance for chaining

**Examples:**

```php
// This will search for a model in frontend
Component::get('com_content')
    ->site()
    ->getModel('Articles');

```
