<?php

namespace JoomlaCli\Test\Console\Joomla\Extension\Installer\Adapter;


use JoomlaCli\Console\Joomla\Extension\Installer;

class LanguageTest extends \PHPUnit_Framework_TestCase
{

    protected $basePath;

    public function setUp()
    {
        $this->basePath = __DIR__ . '/../../../../../../../resources/extensions/';

        $this->target = sys_get_temp_dir() . '/joomla-cli-unit-test';
    }

    public function testInstall()
    {
        $extensionPath = realpath($this->basePath . 'language');
        $manifest = new \SimpleXMLElement(file_get_contents($extensionPath . '/install.xml'));


        $adapter = new Installer\Adapter\Language($extensionPath, $manifest, 'install.xml');
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/language/xx-XX'));
        $this->assertTrue(file_exists($this->target . '/language/xx-XX/xx-XX.ini'));
        $this->assertTrue(file_exists($this->target . '/language/xx-XX/xx-XX.xml'));
    }

    public function testInstallAdmin()
    {
        $extensionPath = realpath($this->basePath . 'admin-language');
        $manifest = new \SimpleXMLElement(file_get_contents($extensionPath . '/install.xml'));

        $adapter = new Installer\Adapter\Language($extensionPath, $manifest, 'install.xml');
        $adapter->install($this->target);

        $this->assertTrue(is_dir($this->target  . '/administrator/language/xx-XX'));
        $this->assertTrue(file_exists($this->target . '/administrator/language/xx-XX/xx-XX.ini'));
        $this->assertTrue(file_exists($this->target . '/administrator/language/xx-XX/xx-XX.xml'));
    }

    public function tearDown()
    {
        if (file_exists($this->target)) {
            $path = escapeshellarg($this->target);
            `rm -rf $path`;
        }
    }


}