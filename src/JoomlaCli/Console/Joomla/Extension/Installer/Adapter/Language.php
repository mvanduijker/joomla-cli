<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer\Adapter;

class Language extends Base
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
        $client = trim(str_replace('/', '', (string)$this->manifest['client']));

        if (!$client) {
            throw new \RuntimeException('No group configured! Can not install language');
        }

        $tag = trim(str_replace('/', '', (string)$this->manifest->tag));

        if (!$tag) {
            throw new \RuntimeException('No tag configured! Can not install language');
        }


        if ($client === 'site') {
            $languageTarget = rtrim($target, '/') . '/language/' . $tag;
        } elseif ($client === 'administrator') {
            $languageTarget = rtrim($target, '/') . '/administrator/language/' . $tag;
        } else {
            throw new \RuntimeException('Invalid language type (client)');
        }

        // install language files
        $this->installLanguageFiles($this->manifest->files, $languageTarget);

        // install pdf font files
        // Couldn't find an example, let's skip

        // install media files
        // joomla code also seems to install media files, let's skip that for now, who would use that?

        return true;
    }

    /**
     * Override installLanguages method, we use it to install core languages
     *
     * @param \SimpleXMLElement $element
     * @param $target where the language files must te copied to example: {fullpath}/adminstrator/language
     */
    protected function installLanguageFiles(\SimpleXMLElement $element, $target)
    {
        $this->fs->mirror($this->path, $target);
    }
}