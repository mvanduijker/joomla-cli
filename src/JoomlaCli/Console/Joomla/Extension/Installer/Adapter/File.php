<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class File extends Base
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



        // install files
        if ($this->manifest->fileset && count($this->manifest->fileset->children())) {
            foreach ($this->manifest->fileset->children() as $files) {
                $this->installFileFiles($files, $target);
            }

        }

        // install manifest
        $this->installManifest($target);


        return true;
    }

    /**
     * Installation / copying files
     *
     * @param \SimpleXMLElement $element
     * @param $target path to where the files must be installed
     * @throws \RuntimeException
     */
    protected function installFileFiles(\SimpleXMLElement $element, $target)
    {
        if ($element['folder']) {
            $source = $this->path . '/' . trim((string)$element['folder'], '/');

            if (!file_exists($source)) {
                throw new \RuntimeException('Source frontend component folder does not exists!');
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
        $target .= '/administrator/manifests/files/' . $this->installFile;
        $this->fs->copy($this->path . '/' . $this->installFile, $target);
    }
}