<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Library extends Base
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

        $name = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $this->manifest->libraryname));

        if (!$name) {
            throw new \RuntimeException('Invalid manifest file, invalid library name!');
        }


        // install files
        if ($this->manifest->files) {
            $this->installLibraryFiles($this->manifest->files, $target . '/libraries/' . $name);

        }

        // install manifest
        $this->installManifest($target);

        return true;
    }

    /**
     * Installation / copying of Library files
     *
     * @param \SimpleXMLElement $element
     * @param $target base path + name of library, for example {joomla-installation-path}/libraries/example
     * @throws \RuntimeException
     */
    protected function installLibraryFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $source = $this->path . '/' . trim((string)$element['folder'], '/');

            if (!file_exists($source)) {
                throw new \RuntimeException('Source library folder does not exists!');
            }

            $path = trim(str_replace('../', '', $element['target']), '/');
            $target .= '/' . $path;

            $this->fs->mkdir($target);
            $this->fs->mirror($source, $target);
        } else {
            // copy per file in files element, when folder is set, it will copy all anyways
            // we silently ignore for now (probably only used for uninstaller)
        }
    }

    /**
     * Install manifest to manifests folder (probably for uninstaller)
     *
     * @param $target string
     */
    protected function installManifest($target)
    {
        $target .= '/administrator/manifests/libraries/' . $this->installFile;
        $this->fs->copy($this->path . '/' . $this->installFile, $target);
    }
}