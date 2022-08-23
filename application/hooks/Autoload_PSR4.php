<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 14:48
 */
class Autoload_PSR4
{
    const DEFAULT_PREFIX = "app";

    /**
     * Register Autoloader
     */
    public static function register($prefix = null)
    {
        $prefix = ($prefix) ? (string)$prefix : self::DEFAULT_PREFIX;

        spl_autoload_register(function ($classname) use ($prefix) {
            // Prefix check
            if (strpos(strtolower($classname), "{$prefix}\\") === 0) {

                // Locate class relative path
                $classname = str_replace("{$prefix}\\", "", $classname);

                $filepath = APPPATH . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($classname, '\\')) . '.php';
                if (file_exists($filepath)) {

                    require $filepath;
                }
            }
        });
    }
}