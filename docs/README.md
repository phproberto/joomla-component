# Component class

> `Phproberto\Joomla\Component\Component`

Component class is intended to ease the management of component related stuff.  

Things like retrieve and change parameters made easy.

* [Methods](#methods)
    * [clearInstance($option)](#clearInstance)
    * [getActive()](#getActive)
    * [getFreshInstance($option)](#getFreshInstance)
    * [getInstance($option)](#getInstance)

## Methods<a id="methods"></a>

### clearInstance($option) <a id="clearInstance"></a>

> Clears a cached instance from the static cache. 

By default components are statically cached to avoid that their information is retrieved multiple times from database in the same time execution. This is the perfect for 99% of the usages but there are special cases where you may want to ensure that you clear the cached instance. 

**Parameters:**

* *$option (required):* Component option. Example: com_content.

**Returns:**

`void`

**Examples:**

```php
// This will store a param in the statically cached instance
Component::getInstance('com_content')
    ->setParam('foo', 'var');

// This will return `var` because instance is cached
$foo = Component::getInstance('com_content')
    ->getParam('foo');

// This will clear the cached instance
Component::clearInstance('com_content');

// This will return `NULL` because cached instance was cleared
$foo = Component::getInstance('com_content')
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

### getFreshInstance($option) <a id="getFreshInstance"></a>

> Retrieve a non-statically-cached instance.

By default components are statically cached to avoid that their information is retrieved multiple times from database in the same time execution. This is the perfect for 99% of the usages but there are special cases where you may want to ensure that you retrieve a non-cachhed instance. 

**Parameters:**

* *$option (required)*: Component option. Example: com_content.

**Returns:**

`Phproberto\Joomla\Component\Component`;

**Examples:**

```php
// This will store a param in the statically cached instance
Component::getInstance('com_content')
    ->setParam('foo', 'var');

// This will return `var` because instance is cached
$foo = Component::getInstance('com_content')
    ->getParam('foo');

// This will return `NULL` because cached instance was cleared
$foo = Component::getFreshInstance('com_content')
    ->getParam('foo');
```

### getInstance($option)<a id="getInstance"></a>

> Retrieve an instance of specific component.

It will return a statically cached instance if component has been already loaded or a fresh if not.

**Parameters:**

* *$option (required)*: Component option. Example: com_content.

**Returns:**

`Phproberto\Joomla\Component\Component`;

**Examples:**

```php
// Retrieve com_content component
$component = Component::getInstance('com_content');

if ($component->getParam('show_title', '1') === '1')
{
    // Do something
}
```