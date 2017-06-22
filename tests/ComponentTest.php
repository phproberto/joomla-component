<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Component\Tests;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Component\Tests\Stubs\Component;

/**
 * Tests ComponentHelper class.
 *
 * @since  __DEPLOY_VERSION__
 */
class ComponentTest extends \TestCaseDatabase
{
	/**
	 * Value of com_content extension_id on database.
	 *
	 * @const
	 */
	const COM_CONTENT_EXTENSION_ID = 22;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * Test constructor.
	 *
	 * @expectedException \InvalidArgumentException
	 *
	 * @return  void
	 */
	public function testInvalidOptionInConstructor()
	{
		$component = new Component('  ');
	}

	/**
	 * Test the cached
	 *
	 * @return  void
	 */
	public function testClearInstance()
	{
		$component = Component::getInstance('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::getInstance('com_content');
		$this->assertEquals('Custom', $component2->getPrefix());
		Component::clearInstance('com_content');

		$component2 = Component::getInstance('com_content');
		$this->assertEquals('Content', $component2->getPrefix());
	}

	/**
	 * Test getActive method.
	 *
	 * @return  void
	 */
	public function testGetActive()
	{
		$component = Component::getActive();
		$this->assertEquals('Content', $component->getPrefix());
	}

	/**
	 * Test getExtension method.
	 *
	 * @return  void
	 */
	public function testGetExtension()
	{
		$component = Component::getInstance('com_content');
		$this->assertEquals(self::COM_CONTENT_EXTENSION_ID, $component->getExtension()->extension_id);

		$reflection = new \ReflectionClass($component);
		$extension = $reflection->getProperty('extension');
		$extension->setAccessible(true);

		$customExtension = (object) array('extension_id' => '33', 'var' => 'foo');

		$extension->setValue($component, $customExtension);
		$this->assertEquals(33, $component->getExtension()->extension_id);
		$this->assertEquals(self::COM_CONTENT_EXTENSION_ID, $component->getExtension(true)->extension_id);
	}

	/**
	 * Test getFreshInstance method.
	 *
	 * @return  void
	 */
	public function testGetFreshInstance()
	{
		$component = Component::getInstance('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::getInstance('com_content');
		$this->assertEquals('Custom', $component2->getPrefix());

		$component2 = Component::getFreshInstance('com_content');
		$this->assertEquals('Content', $component2->getPrefix());
	}

	/**
	 * Test getParams method.
	 *
	 * @return  void
	 */
	public function testGetParams()
	{
		$component = Component::getInstance('com_content');
		$defaultParams = $component->getParams();
		$this->assertNotEquals(0, count($defaultParams->toArray()));

		$reflection = new \ReflectionClass($component);
		$params = $reflection->getProperty('params');
		$params->setAccessible(true);

		$customParams = new Registry(array('foo' => 'var', 'var' => 'foo'));
		$params->setValue($component, $customParams);
		$this->assertEquals($customParams, $component->getParams());
		$this->assertEquals($defaultParams, $component->getParams(true));
	}

	/**
	 * Test the getInstance method.
	 *
	 * @return  void
	 */
	public function testGetInstance()
	{
		$component = Component::getInstance('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$component = Component::getInstance('COM_CONTENT');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::getInstance('com_content');
		$this->assertEquals('Custom', $component2->getPrefix());

		$prefix->setValue($component, null);
		$this->assertEquals('Content', $component2->getPrefix());
	}

	/**
	 * Test getPrefix method.
	 *
	 * @return  void
	 */
	public function testGetPrefix()
	{
		$component = Component::getInstance('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$component = Component::getInstance('com_banners');
		$this->assertEquals('Banners', $component->getPrefix());

		$component = Component::getInstance('com_ComPlex_Option');
		$this->assertEquals('Complex_Option', $component->getPrefix());
	}

	/**
	 * Test getTable method.
	 *
	 * @return  void
	 */
	public function testGetTable()
	{
		$component = Component::getInstance('com_categories');
		$table = $component->getTable('Category');
		$this->assertEquals('CategoriesTableCategory', get_class($table));

		$component = Component::getInstance('com_menus');
		$table = $component->getTable('Menu');
		$this->assertEquals('MenusTableMenu', get_class($table));
	}

	/**
	 * Test getTable method.
	 *
	 * @expectedException \InvalidArgumentException
	 *
	 * @return  void
	 */
	public function testGetTableThrowsException()
	{
		$component = Component::getInstance('com_categories');
		$table = $component->getTable('Inexistent');
	}

	/**
	 * Test saveParams method.
	 *
	 * @return  void
	 */
	public function testSaveParams()
	{
		$component = Component::getInstance('com_content');
		$defaultParams = $component->getParams();
		$this->assertNotEquals(0, count($defaultParams->toArray()));

		$component->setParam('custom-param', 'my-value');
		$component->saveParams();
		$this->assertNotEquals($defaultParams, $component->getParams());

		$component = Component::getFreshInstance('com_content');
		$this->assertEquals('my-value', $component->getParam('custom-param'));
	}
}
