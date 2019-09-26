<?php

namespace SmoDav\Mpesa\Support;

use Composer\Script\Event;

class Installer
{
    public static function install(Event $event)
    {
        $config    = __DIR__ . '/../../../config/mpesa.php';
        $configDir = self::getConfigDirectory($event);

        if (! is_file($configDir . '/mpesa.php')) {
            copy($config, $configDir . '/mpesa.php');
        }
    }

    public static function getConfigDirectory(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $configDir = $vendorDir . '/../config';

        if (! is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        return $configDir;
    }
}
