<?php

namespace JoomlaCli\Test\Console\Joomla\Extension;


use JoomlaCli\Console\Joomla\Extension\Installer;

class InstallerTest extends \PHPUnit_Framework_TestCase
{

    protected $extensionPaths=[];

    public function setUp()
    {
        $basePath = __DIR__ . '/../../../../../resources/extensions/';
        $this->extensionPaths['component'] = realpath($basePath . 'component');
    }

    public function testConstructComponent()
    {
        $installer = new Installer($this->extensionPaths['component']);

        $this->assertInstanceOf('JoomlaCli\Console\Joomla\Extension\Installer\Adapter\Component', $installer->getAdapter());
        $this->assertInstanceOf('JoomlaCli\Console\Joomla\Extension\Installer\AdapterInterface', $installer->getAdapter());
    }
}