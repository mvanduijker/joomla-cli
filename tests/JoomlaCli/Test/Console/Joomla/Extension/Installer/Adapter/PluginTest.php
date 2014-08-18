<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class PluginTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPath;

    protected $installFile;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'plugin');
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/test.xml'));
        $this->installFile = 'test.xml';
        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {
        $adapter = new Installer\Adapter\Plugin($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/plugins/system/test'));
        $this->assertTrue(file_exists($this->target . '/plugins/system/test/test.xml'));
        $this->assertTrue(file_exists($this->target . '/plugins/system/test/language/en-GB/en-GB.plg_system_test.ini'));
        $this->assertTrue(file_exists($this->target . '/plugins/system/test/test.php'));
        $this->assertTrue(file_exists($this->target . '/plugins/system/test/test.scriptfile.php'));
        $this->assertTrue(file_exists($this->target . '/media/plg-system-test/test.png'));
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}