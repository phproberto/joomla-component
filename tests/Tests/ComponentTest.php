<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Component\Tests;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Client\ClientInterface;
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

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
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

		// Ensure that all the tests start with no cached instances
		Component::clearAll();

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
	 * Test admin method switches the component active client.
	 *
	 * @return  void
	 */
	public function testAdminSwitchesClient()
	{
		\JFactory::$application
			->method('isAdmin')
			->willReturn(false);

		$component = Component::get('com_content');
		$this->assertTrue($component->getClient()->isSite());
		$this->assertFalse($component->admin()->getClient()->isSite());
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
	public function testClearRemovesCachedInstance()
	{
		$component = Component::get('com_content');

		$reflection = new \ReflectionClass($component);
		$instances = $reflection->getProperty('instances');
		$instances->setAccessible(true);

		$instancesCount = count($instances->getValue($component)[Component::class]);
		$this->assertEquals(1, $instancesCount);

		Component::clear('com_content');

		$this->assertEquals(array(), $instances->getValue($component)[Component::class]);
	}

	/**
	 * Test that clear method only removes desired cached instance.
	 *
	 * @return  void
	 */
	public function testClearRemovesOnlySpecifiedInstance()
	{
		$component = Component::get('com_content');

		$reflection = new \ReflectionClass($component);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(array('com_content'), array_keys($instances));

		$component = Component::get('com_menus');
		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(array('com_content', 'com_menus'), array_keys($instances));

		Component::clear('com_menus');

		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(array('com_content'), array_keys($instances));
	}

	/**
	 * Test clearAll clears all the cached instances
	 *
	 * @return  void
	 */
	public function testClearAllRemovesCachedInstances()
	{
		$component = Component::get('com_content');

		$reflection = new \ReflectionClass($component);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(1, count($instances));
		$this->assertEquals(array('com_content'), array_keys($instances));

		$component = Component::get('com_menus');
		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(array('com_content', 'com_menus'), array_keys($instances));

		Component::clearAll();

		$this->assertEquals(array(), $instancesProperty->getValue($component));
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
	 * Ensure that getClient returns a client interface.
	 *
	 * @return  void
	 */
	public function testGetClientReturnsClientInterface()
	{
		$component = Component::get('com_content');

		$this->assertInstanceOf(ClientInterface::class, $component->getClient());

		$component->site();
		$this->assertInstanceOf(ClientInterface::class, $component->getClient());

		$component->admin();
		$this->assertInstanceOf(ClientInterface::class, $component->getClient());
	}

	/**
	 * Test getClient method returns a frontend client
	 *
	 * @return  void
	 */
	public function testGetClientReturnsFrontendClient()
	{
		\JFactory::$application
			->method('isAdmin')
			->willReturn(false);

		$component = Component::get('com_content');

		$this->assertTrue($component->getClient()->isSite());
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
		$this->assertTrue($component->getClient()->isAdmin());
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
	public function testGetReturnsInstance()
	{
		$component = Component::get('com_content');
		$this->assertEquals(Component::class, get_class($component));
	}

	/**
	 * Test get returns a cached instance if available.
	 *
	 * @return  void
	 */
	public function testGetReturnsCachedInstance()
	{
		$component = Component::get('com_content');

		$reflection = new \ReflectionClass($component);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = $instancesProperty->getValue($component)[Component::class];
		$this->assertEquals(1, count($instances));
		$this->assertEquals(array('com_content'), array_keys($instances));

		$component2 = Component::get('COM_CONTent');
		$instances = $instancesProperty->getValue($component2)[Component::class];
		$this->assertEquals(1, count($instances));
		$this->assertEquals(array('com_content'), array_keys($instances));
		$this->assertEquals(spl_object_hash($component), spl_object_hash($component2));
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

		$component = Component::get('COM_CONTENT');
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
		$component = Component::get('com_categories')->admin();
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

	/**
	 * Test site method switches the component active client.
	 *
	 * @return  void
	 */
	public function testSiteSwitchesClient()
	{
		\JFactory::$application
			->method('isAdmin')
			->willReturn(true);

		$component = Component::get('com_content');
		$this->assertFalse($component->getClient()->isSite());
		$this->assertTrue($component->site()->getClient()->isSite());
	}
}
