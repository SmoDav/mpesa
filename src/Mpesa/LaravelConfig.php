<?php
/*
 *   This file is part of the Smodav Mpesa library.
 *
 *   Copyright (c) 2016 SmoDav
 *
 *   For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 */
namespace SmoDav\Mpesa;

use Illuminate\Config\Repository;
use SmoDav\Mpesa\Contracts\ConfigurationStore;

/**
 * Class LaravelConfig
 *
 * @category PHP
 *
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class LaravelConfig implements ConfigurationStore
{
    /**
     * @var MpesaRepository
     */
    private $repository;

    /**
     * LaravelConfiguration constructor.
     *
     * @param MpesaRepository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get given config value from the configuration store.
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->repository->get($key, $default);
    }
}
