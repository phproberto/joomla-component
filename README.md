# Joomla! Component Library

> 100% unit tested library to interact with Joomla! Components.

[![Build Status](https://travis-ci.org/phproberto/joomla-component.svg?branch=master)](https://travis-ci.org/phproberto/joomla-component)
[![Code Coverage](https://scrutinizer-ci.com/g/phproberto/joomla-component/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-component/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phproberto/joomla-component/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-component/?branch=master)

## Quickstart

```php
use Phproberto\Joomla\Component;

// Get active component
$component = Component::getActive();

// Get an instance of com_content component. Instances are cached statically to avoid loading duplicated data.
$component = Component::get('com_content');

// You can also get a 100% fresh instance (not cached)
$component = Component::getFresh('com_content');

// You can clear a specific instance from the cache
Component::clear('com_content');

// Or clear all the instances from the cache
Component::clearAll();

// Get component params
$params = $component->getParams();

// Get a single param with '1' as default value
$showTitle = $component->getParam('show_title', '1');

// Set a param value
$component->setParam('show_title', '1');

// Save params to the database
if ($component->saveParams())
{
	echo 'success!';
}

// Do something based on the active client in the component
if ($component->getClient()->isSite())
{
	// Do something
}

// Retrieve a backend model changing the component client
$articlesModel = $component->admin()->getModel('Articles');

// Retrieve a frontend model changing the component client
$archiveModel = $component->site()->getModel('Archive');

// Retrieve a backend table changing the component client
$featuredTable = $component->admin()->getTable('Featured');

// Retrieve extension info from `#__extensions` table
$extension = $component->getExtension();

echo $extension->extension_id;
```

## Requirements

* **PHP 5.5+** 
* **Joomla! CMS v3.7+**

## Documentation

See [docs](./docs/README.md) for fully detailed documentation.

## License

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
