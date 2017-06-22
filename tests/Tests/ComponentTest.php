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
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		$app = $this->getMockApplication();

		\JFactory::$application = $app;
	}
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

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
	 * Test clear method.
	 *
	 * @return  void
	 */
	public function testClear()
	{
		$component = Component::get('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::get('com_content');
		$this->assertEquals('Custom', $component2->getPrefix());
		Component::clear('com_content');

		$component2 = Component::get('com_content');
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
	 * Test getBackendModel method.
	 *
	 * @return  void
	 */
	public function testGetModelReturnsBackendModel()
	{
		$component = Component::get('com_admin')->admin();
		$this->assertEquals('AdminModelProfile', get_class($component->getModel('Profile')));
	}

	/**
	 * Test getModel will return a backend model when backend app is active.
	 *
	 * @return  void
	 */
	public function testGetModelReturnsBackendModelWhenBackendAppIsActive()
	{
		\JFactory::$application
			->method('isAdmin')
			->willReturn(true);

		$component = Component::get('com_admin');
		$this->assertEquals('AdminModelProfile', get_class($component->getModel('Profile')));
	}

	/**
	 * Test getExtension method.
	 *
	 * @return  void
	 */
	public function testGetExtension()
	{
		$component = Component::get('com_content');
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
	public function testGetFresh()
	{
		$component = Component::get('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::get('com_content');
		$this->assertEquals('Custom', $component2->getPrefix());

		$component2 = Component::getFresh('com_content');
		$this->assertEquals('Content', $component2->getPrefix());
	}

	/**
	 * Test getBackendModel method.
	 *
	 * @return  void
	 */
	public function testGetModelReturnsFrontendModel()
	{
		$component = Component::get('com_users')->site();
		$this->assertEquals('UsersModelRegistration', get_class($component->getModel('Registration')));
	}

	/**
	 * Test that getModel throws an exception if model is not found.
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetModelNoModelException()
	{
		$component = Component::get('com_content');
		$this->assertEquals('ContentModelArticles', get_class($component->getModel('Unknown')));
	}

	/**
	 * Test that getModel throws an exception if model has no models folder.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testGetModelNoModelsFolderException()
	{
		$component = Component::get('com_ajax');
		$this->assertEquals('AjaxModelUnknown', get_class($component->getModel('Unknown')));
	}

	/**
	 * Test getParams method.
	 *
	 * @return  void
	 */
	public function testGetParams()
	{
		$component = Component::get('com_content');
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
		$component = Component::get('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$component = Component::get('COM_CONTENT');
		$this->assertEquals('Content', $component->getPrefix());

		$reflection = new \ReflectionClass($component);
		$prefix = $reflection->getProperty('prefix');
		$prefix->setAccessible(true);
		$prefix->setValue($component, 'Custom');

		$component2 = Component::get('com_content');
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
		$component = Component::get('com_content');
		$this->assertEquals('Content', $component->getPrefix());

		$component = Component::get('com_banners');
		$this->assertEquals('Banners', $component->getPrefix());

		$component = Component::get('com_ComPlex_Option');
		$this->assertEquals('Complex_Option', $component->getPrefix());
	}

	/**
	 * Test getTable method.
	 *
	 * @return  void
	 */
	public function testGetBackendTable()
	{
		$component = Component::get('com_categories')->admin();
		$table = $component->getTable('Category');
		$this->assertEquals('CategoriesTableCategory', get_class($table));

		$component = Component::get('com_menus')->admin();
		$table = $component->getTable('Menu');
		$this->assertEquals('MenusTableMenu', get_class($table));
	}

	/**
	 * Test getTable will return a backend table when backend app is active.
	 *
	 * @return  void
	 */
	public function testGetTableReturnsBackendTableWhenBackedAppIsActive()
	{
		\JFactory::$application
			->method('isAdmin')
			->willReturn(true);

		$component = Component::get('com_categories');
		$table = $component->getTable('Category');
		$this->assertEquals('CategoriesTableCategory', get_class($table));
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
		$component = Component::get('com_categories');
		$table = $component->getTable('Inexistent');
	}

	/**
	 * Test saveParams method.
	 *
	 * @return  void
	 */
	public function testSaveParams()
	{
		$component = Component::get('com_content');
		$defaultParams = $component->getParams();
		$this->assertNotEquals(0, count($defaultParams->toArray()));

		$component->setParam('custom-param', 'my-value');
		$component->saveParams();
		$this->assertNotEquals($defaultParams, $component->getParams());

		$component = Component::getFresh('com_content');
		$this->assertEquals('my-value', $component->getParam('custom-param'));
	}
}
