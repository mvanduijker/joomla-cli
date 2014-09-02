<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class LibraryTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPath;

    protected $installFile;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'library');
        $this->installFile = 'test.xml';
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/' . $this->installFile));

        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {
        $adapter = new Installer\Adapter\Library($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/libraries/test'));
        $this->assertTrue(file_exists($this->target . '/libraries/test/test.php'));
        $this->assertTrue(file_exists($this->target . '/libraries/test/Mock/Test.php'));
        $this->assertTrue(file_exists($this->target . '/administrator/manifests/libraries/' . $this->installFile));
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}