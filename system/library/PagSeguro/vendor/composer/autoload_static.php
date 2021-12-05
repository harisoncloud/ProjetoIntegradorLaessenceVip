<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0fce40d06f1dd158bf6d9a4666319356
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'ValdeirPsr\\PagSeguro\\Test\\' => 26,
            'ValdeirPsr\\PagSeguro\\' => 21,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ValdeirPsr\\PagSeguro\\Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests/vendor',
        ),
        'ValdeirPsr\\PagSeguro\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
    );

    public static $prefixesPsr0 = array (
        'C' => 
        array (
            'Curl' => 
            array (
                0 => __DIR__ . '/..' . '/curl/curl/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0fce40d06f1dd158bf6d9a4666319356::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0fce40d06f1dd158bf6d9a4666319356::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit0fce40d06f1dd158bf6d9a4666319356::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit0fce40d06f1dd158bf6d9a4666319356::$classMap;

        }, null, ClassLoader::class);
    }
}