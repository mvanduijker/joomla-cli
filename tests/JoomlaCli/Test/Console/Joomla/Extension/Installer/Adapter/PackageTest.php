<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;
use Symfony\Component\Filesystem\Filesystem;

class PackageTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPath;

    protected $installFile;

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../../../resources/extensions/';
        $this->extensionPath = realpath($basePath . 'package');
        $this->installFile = 'pkg_test.xml';
        $this->manifest = new \SimpleXMLElement(file_get_contents($this->extensionPath . '/' . $this->installFile));

        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {

        $adapter = new Installer\Adapter\Package($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target . '/components/com_test'));
        $this->assertTrue(is_dir($this->target . '/administrator/components/com_test'));
        $this->assertTrue(is_dir($this->target . '/plugins/system/test'));
        $this->assertTrue(is_dir($this->target . '/modules/mod_test'));
        $this->assertTrue(is_dir($this->target . '/templates/test'));

        $this->assertFalse(file_exists(sys_get_temp_dir() . '/com_test'));
    }

    public function testTmpDir()
    {
        $adapter = new Installer\Adapter\Package($this->extensionPath, $this->manifest, $this->installFile);
        $adapter->setTmpDir('/tmp');
        $this->assertEquals('/tmp', $adapter->getTmpDir());

        $adapter = new Installer\Adapter\Package($this->extensionPath, $this->manifest, $this->installFile);
        $this->assertEquals(sys_get_temp_dir(), $adapter->getTmpDir());
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            (new Filesystem())->remove($path);
        }
    }
}
