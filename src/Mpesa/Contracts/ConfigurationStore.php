<?php

namespace SmoDav\Mpesa\Contracts;

/**
 * Interface ConfigurationStore
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
interface ConfigurationStore
{
    /**
     * Get the configuration value from the store or a default value to be supplied.
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default = null);
}
