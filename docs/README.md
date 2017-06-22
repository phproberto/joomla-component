# Component class

> `Phproberto\Joomla\Component\Component`

Component class is intended to ease the management of component related stuff.  

Things like retrieve and change parameters made easy.

* [Methods](#methods)
    * [admin()](#admin)
    * [clear($option)](#clear)
    * [getActive()](#getActive)
    * [getFresh($option)](#getFresh)
    * [get($option)](#get)
    * [getPrefix()](#getPrefix)
    * [getTable($name, array $config = array(), $backend = true)](#getTable)
    * [saveParams()](#saveParams)

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

By default components are statically cached to avoid that their information is retrieved multiple times from database in the same time execution. This is the perfect for 99% of the usages but there are special cases where you may want to ensure that you clear the cached instance. 

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

### getActive() <a id="getActive"></a>

> Try to load active component.

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

### getFresh($option) <a id="getFresh"></a>

> Retrieve a non-statically-cached instance.

By default components are statically cached to avoid that their information is retrieved multiple times from database in the same time execution. This is the perfect for 99% of the usages but there are special cases where you may want to ensure that you retrieve a non-cachhed instance. 

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

### get($option)<a id="get"></a>

> Retrieve an instance of specific component.

It will return a statically cached instance if component has been already loaded or a fresh if not.

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

### getTable($name, array $config = array(), $backend = true) <a id="getTable"></a>

> Retrieve component's table.

Try to find and load a table of the component.

**Parameters:**

* *$name (required)*: Name of the table to load. Example: Menu.
* *$config (optional)*: Custom configuration for the table
* *$backend (optional)*: Try to find the table in component backend?

**Returns:**

`string`

**Examples:**

```php
$table = Component::get('com_banners')->getTable('Banner');
$table = Component::get('com_banners')->getTable('Client');
$table = Component::get('com_menus')->getTable('Menu');
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
