<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Module extends Base
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

        $client = str_replace('/', '', (string)$this->manifest['client']);

        if (!$client) {
            throw new \RuntimeException('No group configured! Can not install plugin');
        }

        $element = null;
        // try to retrieve element
        if (count($this->manifest->files)) {
            foreach($this->manifest->files->children() as $el) {
                if ((string)$el['module']) {
                    $element = str_replace('/', '', (string)$el['module']);
                }
            }
        }

        if (!$element) {
            throw new \RuntimeException('Could not determine module name');
        }

        if ($client === 'site') {
            $moduleTarget = rtrim($target, '/') . '/modules/' . $element;
            $languageTarget = rtrim($target, '/') . '/language';
        } elseif ($client === 'administrator') {
            $moduleTarget = rtrim($target, '/') . '/administrator/modules/' . $element;
            $languageTarget = rtrim($target, '/') . '/administrator/language';
        } else {
            throw new \RuntimeException('Invalid module type (client)');
        }

        $this->installModuleFiles($moduleTarget);

        // copy frontend language files (this seems to be deprecated)

        if ($this->manifest->languages) {
            $this->installLanguageFiles($this->manifest->languages, $languageTarget);
        }

        // copy media files
        if ($this->manifest->media && $this->manifest->media['destination']) {
            $this->installMediaFiles($this->manifest->media, $target);
        }

        return true;
    }

    /**
     * Installation / copying of module files
     *
     * @param $target full path to the extension directory {joomla_path}/modules/{module_name}
     */
    protected function installModuleFiles($target)
    {
        $this->fs->mirror($this->path, $target);
    }
}