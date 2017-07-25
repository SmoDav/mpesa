<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
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
     */
    public function __construct()
    {
        $defaultConfig = require(__DIR__ . '/../../../config/mpesa.php');
        $userConfig    = __DIR__ . '/../../../../../../config/mpesa.php';
        $custom        = [];
        if (\is_file($userConfig)) {
            $custom = require($userConfig);
        }

        $this->config = \array_merge($defaultConfig, $custom);
    }

    /**
     * Get the configuration value.
     *
     * @param      $key
     * @param null $default
     */
    public function get($key, $default = null)
    {
        $itemKey = \explode('.', $key)[1];

        if (isset($this->config[$itemKey])) {
            return $this->config[$itemKey];
        }

        return $default;
    }
}
