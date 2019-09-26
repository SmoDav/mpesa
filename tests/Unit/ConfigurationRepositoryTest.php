<?php

namespace SmoDav\Mpesa\Tests\Unit;

use SmoDav\Mpesa\Native\NativeConfig;
use SmoDav\Mpesa\Repositories\ConfigurationRepository;
use SmoDav\Mpesa\Tests\TestCase;

class ConfigurationRepositoryTest extends TestCase
{
    /**
     * Create the native configuration.
     *
     * @return NativeConfig
     */
    protected function nativeConfig()
    {
        return new NativeConfig(NativeImplementationsTest::CONFIG_FILE);
    }

    /**
     * @return void
     */
    public function testCanConstruct()
    {
        $repository = new ConfigurationRepository($this->nativeConfig());
        $this->assertInstanceOf(ConfigurationRepository::class, $repository);
    }

    /**
     * @return void
     */
    public function testCanExtractConfigAndAccountValues()
    {
        $repository = new ConfigurationRepository($this->nativeConfig());

        $this->assertEquals('test', $repository->config('default'));
        $this->assertTrue($repository->useAccount('test')->getAccountKey('sandbox'));
        $this->assertFalse($repository->useAccount('production')->getAccountKey('sandbox'));

        $this->assertEquals('default', $repository->config('fake_config', 'default'));
        $this->assertEquals(
            'default_account',
            $repository->useAccount('production')->getAccountKey('fake_account_config', 'default_account')
        );
    }

    /**
     * @return void
     */
    public function testCanResolveUrl()
    {
        $repository = new ConfigurationRepository($this->nativeConfig());
        $this->assertEquals(ConfigurationRepository::SANDBOX_URL . 'test-url', $repository->url('test-url'));
        $this->assertEquals(
            ConfigurationRepository::PRODUCTION_URL . 'test-url',
            $repository->useAccount('production')->url('test-url')
        );
    }
}
