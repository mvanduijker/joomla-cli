<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

use JoomlaCli\Console\Joomla\Extension\Installer\AdapterInterface;
use Symfony\Component\Filesystem\Filesystem;

class Component implements AdapterInterface
{

    protected $path;
    protected $manifest;

    public function __construct($path, \SimpleXMLElement $manifest)
    {
        $this->path = $path;

        $this->manifest = $manifest;
    }

    /**
     * @param $target root directory of Joomla installation
     * @throws \RuntimeException
     */
    public function install($target)
    {
        // filter name according to 'cmd' JFilterInput
        $name = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $this->manifest->name));

        if (!$name) {
            throw new \RuntimeException('Invalid manifest file, invalid name!');
        }

        // simulation from JInstallerAdapterComponent->install
        // the check is kind a bogus because underscores are always filtered out in $name
        if (substr($name, 0, 4) == 'com_') {
            $element = $name;
        } else {
            $element = 'com_' . $name;
        }

        if (!$this->manifest->administration) {
            throw new \RuntimeException('Invalid manifest file, administration tag missing');
        }

        // start copying files
        $fs = new Filesystem();

        // copy frontend component files
        if ($this->manifest->files['folder']) {
            $frontendSource = $this->path . '/' . trim((string)$this->manifest->files['folder'], '/');
            $frontendTarget = rtrim($target, '/') . '/components/' . $element;

            $fs->mkdir($frontendTarget);
            $fs->mirror($frontendSource, $frontendTarget);
        }

        // copy administrator component files
        if ($this->manifest->administration->files['folder']) {

            $adminSource = $this->path . '/' . trim((string)$this->manifest->administration->files['folder'], '/');
            $adminTarget = rtrim($target, '/') . '/administrator/components/' . $element;

            $fs->mkdir($adminTarget);
            $fs->mirror($adminSource, $adminTarget);

        }

        // copy media files

        // copy frontend language files

        // copy backend language files

        // copy manifest script

        return true;
    }
}