<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd95a3e01a79be44b35076b3590b8109b
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd95a3e01a79be44b35076b3590b8109b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd95a3e01a79be44b35076b3590b8109b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd95a3e01a79be44b35076b3590b8109b::$classMap;

        }, null, ClassLoader::class);
    }
}
