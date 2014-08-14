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

        $frontendTarget = rtrim($target, '/') . '/components/' . $element;
        $adminTarget = rtrim($target, '/') . '/administrator/components/' . $element;

        // start copying files
        $fs = new Filesystem();

        // copy frontend component files
        if ($this->manifest->files) {

            if ($this->manifest->files['folder']) {
                $frontendSource = $this->path . '/' . trim((string)$this->manifest->files['folder'], '/');

                if (!file_exists($frontendSource)) {
                    throw new \RuntimeException('Source frontend component folder does not exists!');
                }

                $fs->mkdir($frontendTarget);
                $fs->mirror($frontendSource, $frontendTarget);
            } else {
                // copy per file in files element, when folder is set, it will copy all anyways
                // TODO
            }
        }

        // copy administrator component files
        if ($this->manifest->administration->files) {

            if ($this->manifest->administration->files['folder']) {
                $adminSource = $this->path . '/' . trim((string)$this->manifest->administration->files['folder'], '/');

                if (!file_exists($adminSource)) {
                    throw new \RuntimeException('Source adminstrator component folder does not exists!');
                }

                $fs->mkdir($adminTarget);
                $fs->mirror($adminSource, $adminTarget);
            } else {
                // copy per file in files element, when folder is set, it will copy all anyways
                // TODO
            }
        }

        // copy media files TODO

        // copy frontend language files TODO

        // copy backend language files TODO

        // copy manifest script
        if ($this->manifest->scriptfile) {
            $scriptfile = (string)$this->manifest->scriptfile;
            if ($scriptfile && file_exists($this->path . '/' . $scriptfile)) {
                $scriptFileTarget = $adminTarget . '/' . $scriptfile;
                if (!file_exists(dirname($scriptFileTarget))) {
                    $fs->mkdir(dirname($scriptFileTarget));
                }
                $fs->copy($this->path . '/' . $scriptfile, $scriptFileTarget);
            }
        }

        return true;
    }
}