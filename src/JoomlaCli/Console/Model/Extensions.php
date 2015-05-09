<?php

namespace JoomlaCli\Console\Model;

/**
 * Class Extensions
 *
 * @package JoomlaCli\Console\Model
 */
class Extensions
{
    /**
     * @var string
     */
    protected $tmpDir;

    /**
     * Constructor
     *
     * @param bool $allowModifications readonly flag
     */
    public function __construct($tmpDir = null)
    {
        $this->tmpDir = $tmpDir;

    }

    public function installArchive($path)
    {
        $isRemote = false;
        $file = $path;

        // check if a url, if a url we are going to save to a tmp file
        if (preg_match('/^http(s){0,1}\:\/\//i', $path)) {
            $isRemote = true;

            //file_get_contents('')

            //
        }
    }

}