<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Plugin extends Base
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

        $group = str_replace('/', '', (string)$this->manifest['group']);

        if (!$group) {
            throw new \RuntimeException('No group configured! Can not install plugin');
        }

        $element = null;
        // try to retrieve element
        if (count($this->manifest->files)) {
            foreach($this->manifest->files->children() as $el) {
                if ((string)$el['plugin']) {
                    $element = str_replace('/', '', (string)$el['plugin']);
                }
            }
        }

        if (!$element) {
            throw new \RuntimeException('Could not determine plugin name');
        }

        $pluginTarget = rtrim($target, '/') . '/plugins/' . $group . '/' . $element;
        $this->installPluginFiles($pluginTarget);

        // copy frontend language files (this seems to be deprecated)
//        $languageTarget = rtrim($target, '/') . '/language';
//        if ($this->manifest->languages) {
//            $this->installLanguageFiles($this->manifest->languages, $languageTarget);
//        }

        // copy media files
        if ($this->manifest->media && $this->manifest->media['destination']) {
            $this->installMediaFiles($this->manifest->media, $target);
        }

        return true;
    }

    /**
     * Installation / copying of plugin files
     *
     * @param $target full path to the extension directory {joomla_path}/plugins/{group}/{plugin}
     */
    protected function installPluginFiles($target)
    {
        $this->fs->mirror($this->path, $target);
    }
}