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
    /**
     * Mpesa configuration file.
     *
     * @var array
     */
    protected $config;

    /**
     * NativeConfig constructor.
     *
     * @param string|null $configPath
     */
    public function __construct($configPath = null)
    {
        $defaultConfig = require __DIR__ . '/../../../config/mpesa.php';
        $configPath = $configPath ?: __DIR__ . '/../../../../../../config/mpesa.php';
        $custom = [];

        if (is_file($configPath)) {
            $custom = require $configPath;
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
