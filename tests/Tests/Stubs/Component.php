<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Component\Tests\Stubs;

use Phproberto\Joomla\Component\Component as BaseComponent;

/**
 * Custom Component class to ease testability.
 *
 * @since  __DEPLOY_VERSION__
 */
class Component extends BaseComponent
{
	/**
	 * Get the active component option. Mainly for testing purposes.
	 *
	 * @return  string
	 */
	protected static function getActiveOption()
	{
		return 'com_content';
	}
}
