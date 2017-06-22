<?php
/**
 * Joomla! component.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Component;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Client\Client;
use Phproberto\Joomla\Client\Site;
use Phproberto\Joomla\Client\Administrator;
use Phproberto\Joomla\Client\ClientInterface;
use Phproberto\Joomla\Traits;

defined('JPATH_PLATFORM') || die;

/**
 * Table finder.
 *
 * @since  __DEPLOY_VERSION__
 */
class Component
{
	use Traits\HasExtension, Traits\HasParams;

	/**
	 * Component option. Example: com_content
	 *
	 * @var  string
	 */
	protected $option;

	/**
	 * Component prefix for classes, etc.
	 *
	 * @var  string
	 */
	protected $prefix;

	/**
	 * Active client.
	 *
	 * @var  ClientInterface
	 */
	protected $client;

	/**
	 * Cached instances.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   string           $option  Component option
	 * @param   ClientInterface  $client  Client
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function __construct($option, ClientInterface $client = null)
	{
		$option = trim(strtolower($option));

		if (empty($option))
		{
			throw new \InvalidArgumentException(__CLASS__ . ': Empty component option.');
		}

		$this->client = $client ?: Client::active();
		$this->option = $option;
	}

	/**
	 * Switch to admin client.
	 *
	 * @return  self
	 */
	public function admin()
	{
		$this->client = new Administrator;

		return $this;
	}

	/**
	 * Clear a singleton instance.
	 *
	 * @param   string  $option  Component option
	 *
	 * @return  void
	 */
	public static function clear($option)
	{
		unset(static::$instances[get_called_class()][$option]);
	}

	/**
	 * Get the active component.
	 *
	 * @return  $this
	 *
	 * @throws  \InvalidArgumentException
	 */
	public static function getActive()
	{
		return static::get(static::getActiveOption());
	}

	/**
	 * Get the active component option. Isolated for testing purposes.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	protected static function getActiveOption()
	{
		return \JApplicationHelper::getComponentName();
	}

	/**
	 * Ensure that we retrieve a non-statically-cached instance.
	 *
	 * @param   string  $option   Component option
	 *
	 * @return  $this
	 */
	public static function getFresh($option)
	{
		static::clear($option);

		return static::get($option);
	}

	/**
	 * Get a singleton instance.
	 *
	 * @param   string  $option  Component option
	 *
	 * @return  $this
	 */
	public static function get($option)
	{
		$option = trim(strtolower($option));

		$class = get_called_class();

		if (empty(static::$instances[$class][$option]))
		{
			static::$instances[$class][$option] = new static($option);
		}

		return static::$instances[$class][$option];
	}

	/**
	 * Get a model of this component.
	 *
	 * @param   string  $name    Name of the model.
	 * @param   array   $config  Optional array of configuration for the model
	 *
	 * @return  \JModelLegacy
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getModel($name, array $config = array('ignore_request' => true))
	{
		$prefix = $this->getPrefix() . 'Model';

		\JModelLegacy::addIncludePath($this->getModelsFolder(), $prefix);

		try
		{
			\JTable::addIncludePath($this->getTablesFolder());
		}
		catch (\Exception $e)
		{
			// There are models with no associated tables
		}

		$model = \JModelLegacy::getInstance($name, $prefix, $config);

		if (!$model instanceof \JModel && !$model instanceof \JModelLegacy)
		{
			throw new \InvalidArgumentException(
				sprintf("Cannot find the model `%s` in `%s` component's %s folder.", $name, $this->option, $this->client->getName())
			);
		}

		return $model;
	}

	/**
	 * Get the folder where the models are.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException  If not found
	 */
	protected function getModelsFolder()
	{
		$folder = $this->client->getFolder() . '/components/' . $this->option . '/models';

		if (is_dir($folder))
		{
			return $folder;
		}

		throw new \RuntimeException(
			sprintf("Cannot find the models folder for `%s` component in `%s` folder.", $this->option, $this->client->getName())
		);
	}

	/**
	 * Get the folder where the tables are.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException  If not found
	 */
	protected function getTablesFolder()
	{
		$folder = $this->client->getFolder() . '/components/' . $this->option . '/tables';

		if (is_dir($folder))
		{
			return $folder;
		}

		throw new \RuntimeException(
			sprintf("Cannot find the tables folder for `%s` component in `%s` folder.", $this->option, $this->client->getName())
		);
	}

	/**
	 * Get the component prefix.
	 *
	 * @return  string
	 */
	public function getPrefix()
	{
		if (null === $this->prefix)
		{
			$parts = array_map(
				function ($part)
				{
					return ucfirst(strtolower($part));
				},
				explode('_', substr($this->option, 4))
			);

			$this->prefix = implode('_', $parts);
		}

		return $this->prefix;
	}

	/**
	 * Get a component table.
	 *
	 * @param   string   $name     Name of the table to load. Example: Article
	 * @param   array    $config   Optional array of configuration for the table
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getTable($name, array $config = array())
	{
		$prefix = $this->getPrefix() . 'Table';

		\JTable::addIncludePath($this->getTablesFolder());

		$table = \JTable::getInstance($name, $prefix, $config);

		if (!$table instanceof \JTable)
		{
			throw new \InvalidArgumentException(
				sprintf("Cannot find the table `%s` in `%s` component's `%s` folder.", $prefix . $name, $this->option, $this->client->getName())
			);
		}

		return $table;
	}

	/**
	 * Load extension from DB.
	 *
	 * @return  \stdClass
	 */
	protected function loadExtension()
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__extensions')
			->where('type = ' . $db->quote('component'))
			->where('element = ' . $db->q($this->option));

		$db->setQuery($query);

		return $db->loadObject() ?: new \stdClass;
	}

	/**
	 * Load parameters from database.
	 *
	 * @return  Registry
	 */
	protected function loadParams()
	{
		return new Registry($this->getExtensionProperty('params', array()));
	}

	/**
	 * Save parameters to database.
	 *
	 * @return  Registry
	 */
	public function saveParams()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update('#__extensions')
			->set('params = ' . $db->q($this->getParams()->toString()))
			->where('type = ' . $db->quote('component'))
			->where('element = ' . $db->q($this->option));

		$db->setQuery($query);

		return $db->execute() ? true : false;
	}

	/**
	 * Switch to site client.
	 *
	 * @return  self
	 */
	public function site()
	{
		$this->client = new Site;

		return $this;
	}
}
