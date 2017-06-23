# Joomla! Component Library

> Library to interact with Joomla! Components.

[![Build Status](https://travis-ci.org/phproberto/joomla-component.svg?branch=master)](https://travis-ci.org/phproberto/joomla-component)
[![Code Coverage](https://scrutinizer-ci.com/g/phproberto/joomla-component/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-component/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phproberto/joomla-component/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-component/?branch=master)

## Quickstart

```php
use Phproberto\Joomla\Component;

// Get active component
$component = Component::getActive();

// Get an instance of com_content component
$component = Component::get('com_content');

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

// Retrieve a backend model
$articlesModel = $component->admin()->getModel('Articles');

// Retrieve a frontend model
$archiveModel = $component->site()->getModel('Archive');

// Retrieve a backend table
$featuredTable = $component->admin()->getTable('Featured');

// Retrieve extension info from `#__extensions` table
$extension = $component->getExtension();

echo $extension->extension_id;
```

## Requirements

* **PHP 5.4+** Due to the use of traits
* **Joomla! CMS v3.7+**

## Documentation

See [docs](./docs/README.md) for detailed information.

## License

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
