<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Template extends Base
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
        $name = str_replace(' ', '_', $name); // this is done in joomla, probably will never have spaces, becuase of regexp

        if (!$name) {
            throw new \RuntimeException('Invalid manifest file, invalid name!');
        }

        $client = (string) $this->manifest['client'];
        if (!in_array($client, ['administrator', 'site'])) {
            throw new \RuntimeException('Invalid manifest file, invalid client!');
        }


        if ($client === 'administrator') {
            $targetFiles = $target . '/administrator/templates/' . $name;
            $languageTarget = $target . '/administrator/language';
        } else {
            $targetFiles = $target . '/templates/' . $name;
            $languageTarget = $target . '/language';
        }


        // install files
        $this->installTemplateFiles($targetFiles);

        // install media files
        if ($this->manifest->media && $this->manifest->media['destination']) {
            $this->installMediaFiles($this->manifest->media, $target);
        }

        // install language files
        if ($this->manifest->languages) {
            $this->installLanguageFiles($this->manifest->languages, $languageTarget);
        }


        return true;
    }

    /**
     * Installation / copying of module files
     *
     * @param $target full path to the template directory {joomla_path}/templates/{template_name}
     */
    protected function installTemplateFiles($target)
    {
        $this->fs->mirror($this->path, $target);
    }
}