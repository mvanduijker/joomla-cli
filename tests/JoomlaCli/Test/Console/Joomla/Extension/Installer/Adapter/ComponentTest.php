<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class ComponentTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPath;

    protected $installFile;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'component');
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/test.xml'));
        $this->installFile = 'test.xml';
        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';

    }

    public function testInstall()
    {

        $adapter = new Installer\Adapter\Component($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target . '/components/com_test'));
        $this->assertTrue(file_exists($this->target . '/components/com_test/test.php'));
        $this->assertTrue(file_exists($this->target . '/components/com_test/views/default/tmpl/default.php'));

        $this->assertTrue(is_dir($this->target . '/administrator/components/com_test'));
        $this->assertTrue(file_exists($this->target . '/administrator/components/com_test/config.xml'));
        $this->assertTrue(file_exists($this->target . '/administrator/components/com_test/helpers/help.php'));

        $this->assertTrue(file_exists($this->target . '/administrator/components/com_test/install.script.php'));

        $this->assertTrue(file_exists($this->target . '/media/com_test/test.png'));

        $this->assertTrue(file_exists($this->target . '/language/en-GB/en-GB.com_example.ini'));

        $this->assertTrue(file_exists($this->target . '/administrator/language/en-GB/en-GB.com_example.ini'));

    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}