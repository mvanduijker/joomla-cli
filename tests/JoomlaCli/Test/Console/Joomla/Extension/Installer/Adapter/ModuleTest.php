<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class ModuleTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPath;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'module');
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/mod_test.xml'));
        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {
        $adapter = new Installer\Adapter\Module($this->extensionPath, $this->manifest);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/modules/mod_test'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/mod_test.xml'));
        $this->assertTrue(file_exists($this->target . '/language/en-GB/en-GB.mod_test.ini'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/mod_test.php'));
        $this->assertTrue(file_exists($this->target . '/modules/mod_test/test.scriptfile.php'));
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