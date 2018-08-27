<?php

namespace SmoDav\Mpesa\Native;

use SmoDav\Mpesa\Contracts\ConfigurationStore;

/**
 * Class NativeConfig
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class NativeConfig implements ConfigurationStore
{
    //TODO: change implementation so user can enter the location.

    /**
     * Mpesa configuration file.
     *
     * @var array
     */
    protected $config;

    /**
     * NativeConfig constructor.
     */
    public function __construct()
    {
        $defaultConfig = require __DIR__ . '/../../../config/mpesa.php';
        $userConfig    = __DIR__ . '/../../../../../../config/mpesa.php';
        $custom = [];
        if (is_file($userConfig)) {
            $custom = require $userConfig;
        }

        $this->config = ['mpesa' => array_merge($defaultConfig, $custom)];
    }

    /**
     * Get the configuration value.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $pieces = explode('.', $key);
        $config = $this->config;

        foreach ($pieces as $piece) {
            if (!isset($config[$piece])) {
                return $default;
            }

            $config = $config[$piece];
        }

        return $config;
    }
}
