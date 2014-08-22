<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

    protected $basePath;
    protected $extensionPath;
    protected $installFile;
    protected $manifest;
    protected $target;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'template');
        $this->installFile = 'templateDetails.xml';
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/' . $this->installFile));

        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {

        $adapter = new Installer\Adapter\Template($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/templates/test'));
        $this->assertTrue(file_exists($this->target . '/templates/test/index.php'));
        $this->assertTrue(file_exists($this->target . '/language/en-GB/en-GB.tpl_test.ini'));
        $this->assertTrue(file_exists($this->target . '/language/en-GB/en-GB.tpl_test.sys.ini'));
        $this->assertTrue(file_exists($this->target . '/media/tpl_test/test.png'));
    }

    public function testInstallAdmin()
    {
        $this->manifest['client'] = 'administrator';
        $adapter = new Installer\Adapter\Template($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/administrator/templates/test'));
        $this->assertTrue(file_exists($this->target . '/administrator/templates/test/index.php'));
        $this->assertTrue(file_exists($this->target . '/administrator/language/en-GB/en-GB.tpl_test.ini'));
        $this->assertTrue(file_exists($this->target . '/administrator/language/en-GB/en-GB.tpl_test.sys.ini'));
        $this->assertTrue(file_exists($this->target . '/media/tpl_test/test.png'));
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}