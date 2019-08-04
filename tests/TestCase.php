<?php

namespace Tests;

use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;


    /**
     * @afterClass
     */
    public static function tearDownDeleteTestFolder() : void
    {
        self::deleteDirectory('./testing');
    }


    /**
     * Remove directory.
     *
     * @param $dirname
     *
     * @return bool
     */
    public static function deleteDirectory($dirname) : bool
    {
        $dirHandle = false;
        if (is_dir($dirname)) {
            $dirHandle = opendir($dirname);
        }

        if ( ! $dirHandle) {
            return false;
        }

        while ($file = readdir($dirHandle)) {
            if ($file !== "." && $file !== "..") {
                if ( ! is_dir($dirname . "/" . $file)) {
                    unlink($dirname . "/" . $file);
                } else {
                    self::deleteDirectory($dirname . '/' . $file);
                }
            }
        }

        closedir($dirHandle);
        rmdir($dirname);

        return true;
    }
}
