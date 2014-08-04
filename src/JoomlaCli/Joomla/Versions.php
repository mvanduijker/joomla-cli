<?php

namespace JoomlaCli\Joomla;

/**
 * Class Versions to do stuff with released Joomla versions
 *
 * @package JoomlaCli\Joomla
 */
class Versions
{
    protected $joomlaRepository = 'https://github.com/joomla/joomla-cms.git';

    /**
     * Get list of releases
     *
     * @return array
     */
    public function get()
    {

        $repository = escapeshellarg($this->joomlaRepository);
        $result = `git ls-remote $repository | grep -E 'refs/(tags|heads)' | grep -v '{}'`;
        $refs   = array_filter(explode(PHP_EOL, $result));

        $versions = [];
        $pattern  = '/^[a-z0-9]+\s+refs\/(heads|tags)\/([a-z0-9\.\-_]+)$/i';
        foreach ($refs as $ref) {
            if (preg_match($pattern, $ref, $matches)) {

                $type = isset($versions[$matches[1]]) ? $versions[$matches[1]] : array();

                if ($matches[1] == 'tags') {
                    if (preg_match('/^1\.*/', $matches[2]) || !preg_match('/^\d\.\d+/', $matches[2])) {
                        continue;
                    }
                }

                if ($matches[1] == 'heads') {
                    $tarBall = 'https://github.com/joomla/joomla-cms/tarball/' . $matches[2];
                } else {
                    $tarBall = 'https://github.com/joomla/joomla-cms/archive/' . $matches[2] . '.tar.gz';
                }

                $versions[$matches[1]][$matches[2]] = $tarBall;
            }
        }

        return $versions;
    }

    /**
     * Get a release from the release list of given version
     * Using .* will try to find latest of particular major / major.minor version
     * For example 3.3.* will find the latest 3.3 release
     *
     * @param string $version strict name or part of version with .*
     *
     * @return array|null
     */
    public function getVersion($version)
    {
        if (!is_scalar($version)) {
            return null;
        }

        $versions = $this->get();

        // first check on exact matches
        if (array_key_exists($version, $versions['heads'])) {
            return [$version => $versions['heads'][$version]];
        }

        if (array_key_exists($version, $versions['tags'])) {
            return [$version => $versions['tags'][$version]];
        }


        $latest = null;
        $matches = [];
        if (preg_match('/^(\d+)\.(\d+|\*)(\.\*)?$/', $version, $matches)) {
            foreach (array_keys($versions['tags']) as $tag) {
                if (isset($matches[1]) && isset($matches[2])) {
                    // check if major.minor.x or major.x
                    if ($matches[2] === '*') {
                        $check = preg_match('/^'.$matches[1] . '\.*/', $tag);
                    } else {
                        $check = preg_match('/^'.$matches[1] . '\.' . $matches[2] . '\.*/', $tag);
                    }

                    if ($check) {
                        if ($latest === null) {
                            $latest = $tag;
                        } elseif (version_compare($tag, $latest, '>')) {
                            $latest = $tag;
                        }
                    }
                }
            }

            if ($latest !== null) {
                return [$latest => $versions['tags'][$latest]];
            } else {
                return null;
            }
        }

    }
}
