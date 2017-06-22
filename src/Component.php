<?php
/**
 * Joomla! component.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Component;

use Joomla\Registry\Registry;
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
	 * Cached instances.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   string  $option  Component option
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function __construct($option)
	{
		$option = trim(strtolower($option));

		if (empty($option))
		{
			throw new \InvalidArgumentException(__CLASS__ . ': Empty component option.');
		}

		$this->option = $option;
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
	 * Alias method to get a backend model.
	 *
	 * @param   string   $name     Name of the model.
	 * @param   array    $config   Optional array of configuration for the model
	 *
	 * @return  \JModelLegacy
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getBackendModel($name, array $config = array('ignore_request' => true))
	{
		return $this->getModel($name, $config, true);
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
	 * Alias method to get a frontend model.
	 *
	 * @param   string   $name     Name of the model.
	 * @param   array    $config   Optional array of configuration for the model
	 *
	 * @return  \JModelLegacy
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getFrontendModel($name, array $config = array('ignore_request' => true))
	{
		return $this->getModel($name, $config);
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
	 * @param   string   $name     Name of the model.
	 * @param   array    $config   Optional array of configuration for the model
	 * @param   boolean  $backend  Search in backend folder?
	 *
	 * @return  \JModelLegacy
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getModel($name, array $config = array('ignore_request' => true), $backend = false)
	{
		$prefix = $this->getPrefix() . 'Model';

		\JModelLegacy::addIncludePath($this->getModelsFolder($backend), $prefix);

		try
		{
			\JTable::addIncludePath($this->getTablesFolder($backend));
		}
		catch (\Exception $e)
		{
		}

		$model = \JModelLegacy::getInstance($name, $prefix, $config);

		if (!$model instanceof \JModel && !$model instanceof \JModelLegacy)
		{
			$clientName = $backend ? 'backend' : 'frontend';

			throw new \InvalidArgumentException(
				sprintf("Cannot find the model `%s` in `%s` component's %s.", $name, $this->option, $clientName)
			);
		}

		return $model;
	}

	/**
	 * Get the folder where the models are.
	 *
	 * @param   boolean  $backend  Search in backend?
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException  If not found
	 */
	protected function getModelsFolder($backend = false)
	{
		$baseFolder = $backend ? JPATH_ADMINISTRATOR : JPATH_SITE;

		$folder = $baseFolder . '/components/' . $this->option . '/models';

		if (is_dir($folder))
		{
			return $folder;
		}

		throw new \RuntimeException(
			sprintf('Cannot find the models folder for component %s.', $this->option)
		);
	}

	/**
	 * Get the folder where the tables are.
	 *
	 * @param   boolean  $backend  Search in backend?
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException  If not found
	 */
	protected function getTablesFolder($backend = false)
	{
		$baseFolder = $backend ? JPATH_ADMINISTRATOR : JPATH_SITE;

		$folder = $baseFolder . '/components/' . $this->option . '/tables';

		if (is_dir($folder))
		{
			return $folder;
		}

		throw new \RuntimeException(
			sprintf('Cannot find the tables folder for component %s.', $this->option)
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
	 * @param   boolean  $backend  Search in backend folder?
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException  If not found
	 */
	public function getTable($name, array $config = array(), $backend = true)
	{
		$prefix = $this->getPrefix() . 'Table';

		$baseFolder = $backend ? JPATH_ADMINISTRATOR : JPATH_SITE;

		\JTable::addIncludePath($baseFolder . '/components/' . $this->option . '/tables');

		$table = \JTable::getInstance($name, $prefix, $config);

		if (!$table instanceof \JTable)
		{
			throw new \InvalidArgumentException(
				sprintf('Cannot find the table %s in component %s.', $prefix . $name, $this->option)
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
}
