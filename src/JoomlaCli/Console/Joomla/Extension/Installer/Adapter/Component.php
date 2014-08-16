<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Component extends Base
{
    /**
     * Main install procedure of copying the files
     *
     * @param $target root directory of Joomla installation
     * @throws \RuntimeException
     * @return void
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
        $frontendLanguageTarget = rtrim($target, '/') . '/language';
        $adminLanguageTarget = rtrim($target, '/') . '/administrator/language';

        // start copying files

        // copy frontend component files
        if ($this->manifest->files) {
            $this->installComponentFiles($this->manifest->files, $frontendTarget);
        }

        // copy administrator component files
        if ($this->manifest->administration->files) {
            $this->installComponentFiles($this->manifest->administration->files, $adminTarget);
        }

        // copy media files
        if ($this->manifest->media && $this->manifest->media['destination']) {
            $this->installMediaFiles($this->manifest->media, $target);
        }

        // copy frontend language files
        if ($this->manifest->languages) {
            $this->installLanguageFiles($this->manifest->languages, $frontendLanguageTarget);
        }

        // copy backend language files
        if ($this->manifest->administration->languages) {
            $this->installLanguageFiles($this->manifest->languages, $adminLanguageTarget);
        }

        // copy manifest script
        if ($this->manifest->scriptfile) {
            $this->installScriptFile($this->manifest->scriptfile, $adminTarget);
        }

        return true;
    }

    /**
     * Installation / copying of component files
     *
     * @param \SimpleXMLElement $element
     * @param $target base path + name of component directory, for example {joomla-installation-path}/components/com_example
     * @throws \RuntimeException
     */
    protected function installComponentFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $source = $this->path . '/' . trim((string)$element['folder'], '/');

            if (!file_exists($source)) {
                throw new \RuntimeException('Source frontend component folder does not exists!');
            }

            $this->fs->mkdir($target);
            $this->fs->mirror($source, $target);
        } else {
            // copy per file in files element, when folder is set, it will copy all anyways
            // we silently ignore for now
        }
    }
}