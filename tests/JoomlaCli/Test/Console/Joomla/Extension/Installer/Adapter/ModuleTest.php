<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class ModuleTest extends \PHPUnit_Framework_TestCase
{

    protected $basePath;

    public function setUp()
    {
        $this->basePath = __DIR__ . '/../../../../../../../resources/extensions/';

        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {
        $extensionPath = realpath($this->basePath . 'module');
        $manifest = new \SimpleXMLElement(file_get_contents($extensionPath . '/mod_test.xml'));

        $adapter = new Installer\Adapter\Module($extensionPath, $manifest);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/modules/mod_test'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/mod_test.xml'));
        $this->assertTrue(file_exists($this->target . '/language/en-GB/en-GB.mod_test.ini'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/mod_test.php'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/test.scriptfile.php'));
        $this->assertTrue(file_exists($this->target . '/media/mod_test/test.png'));
    }

    public function testInstallAdmin()
    {
        $extensionPath = realpath($this->basePath . 'admin-module');
        $manifest = new \SimpleXMLElement(file_get_contents($extensionPath . '/mod_test.xml'));

        $adapter = new Installer\Adapter\Module($extensionPath, $manifest);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/administrator/modules/mod_test'));
        $this->assertTrue(file_exists($this->target . '/administrator/modules/mod_test/mod_test.xml'));
        $this->assertTrue(file_exists($this->target . '/administrator/language/en-GB/en-GB.mod_test.ini'));
        $this->assertTrue(file_exists($this->target . '/administrator/modules/mod_test/mod_test.php'));
        $this->assertTrue(file_exists($this->target . '/administrator/modules/mod_test/test.scriptfile.php'));
        $this->assertTrue(file_exists($this->target . '/media/mod_test/test.png'));
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}