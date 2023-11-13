<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f291dcd9fcfac10a8a3c656a8e8799e
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '9b38cf48e83f5d8f60375221cd213eee' => __DIR__ . '/..' . '/phpstan/phpstan/bootstrap.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '0d59ee240a4cd96ddbb4ff164fccea4d' => __DIR__ . '/..' . '/symfony/polyfill-php73/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SzepeViktor\\PHPStan\\WordPress\\' => 30,
            'Symfony\\Polyfill\\Php73\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'PHPStan\\ExtensionInstaller\\' => 27,
        ),
        'I' => 
        array (
            'Ilovepdf\\' => 9,
            'Ilove_Pdf_Includes\\' => 19,
            'Ilove_Pdf_Admin\\' => 16,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SzepeViktor\\PHPStan\\WordPress\\' => 
        array (
            0 => __DIR__ . '/..' . '/szepeviktor/phpstan-wordpress/src',
        ),
        'Symfony\\Polyfill\\Php73\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php73',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'PHPStan\\ExtensionInstaller\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpstan/extension-installer/src',
        ),
        'Ilovepdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/ilovepdf/ilovepdf-php/src',
        ),
        'Ilove_Pdf_Includes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'Ilove_Pdf_Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/admin',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'JsonException' => __DIR__ . '/..' . '/symfony/polyfill-php73/Resources/stubs/JsonException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0f291dcd9fcfac10a8a3c656a8e8799e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0f291dcd9fcfac10a8a3c656a8e8799e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0f291dcd9fcfac10a8a3c656a8e8799e::$classMap;

        }, null, ClassLoader::class);
    }
}