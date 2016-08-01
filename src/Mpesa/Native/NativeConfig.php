<?php

namespace SmoDav\Mpesa\Native;

use SmoDav\Mpesa\Contracts\ConfigurationStore;

/**
 * Class NativeConfig
 *
 * @category PHP
 * @package  SmoDav\Mpesa\Native
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
     */
    public function __construct()
    {
        $this->config = require(__DIR__ . '/../../../config/mpesa.php');
    }

    /**
     * Get the configuration value.
     *
     * @param      $key
     * @param null $default
     *
     * @return null
     */
    public function get($key, $default = null)
    {
        $itemKey = explode('.', $key)[1];

        if (isset($this->config[$itemKey])) {
            return $this->config[$itemKey];
        }

        return $default;
    }
}
