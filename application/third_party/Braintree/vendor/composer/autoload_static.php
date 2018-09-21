<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit458f9937a4c683170322b3f50913bfcd
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Braintree\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Braintree\\' => 
        array (
            0 => __DIR__ . '/..' . '/braintree/braintree_php/lib/Braintree',
        ),
    );

    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Braintree' => 
            array (
                0 => __DIR__ . '/..' . '/braintree/braintree_php/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit458f9937a4c683170322b3f50913bfcd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit458f9937a4c683170322b3f50913bfcd::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit458f9937a4c683170322b3f50913bfcd::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}