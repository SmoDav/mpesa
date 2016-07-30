<?php

namespace SmoDav\MPesa;

use Illuminate\Config\Repository;
use SmoDav\MPesa\Contracts\ConfigurationStore;

/**
 * Class LaravelConfig
 *
 * @category PHP
 * @package  SmoDav\MPesa
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class LaravelConfig implements ConfigurationStore
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * LaravelConfiguration constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get given config value from the configuration store.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->repository->get($key, $default);
    }
}
