<?php

namespace JoomlaCli\Console\Joomla\Extension\Installer;

interface AdapterInterface
{
    public function __construct($path, \SimpleXMLElement $manifest, $installFile);
    public function install($target);
}