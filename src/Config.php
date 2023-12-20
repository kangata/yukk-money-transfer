<?php

declare(strict_types=1);

namespace QuetzalStudio\YUKK\MoneyTransfer;

class Config
{
    private string $name = 'yukk_money_transfer';

    private string $environment;

    private string $clientId;

    private string $clientSecret;

    private string $baseUrl;

    protected static ?Config $instance = null;

    public function __construct(array $options = [])
    {
        if (empty($options)) {
            $this->useDefaultConfig();
        } else {
            $this->useCustomConfig($options);
        }
    }

    /**
     * Setup with default config
     */
    public function useDefaultConfig(): void
    {
        $this->environment = config("{$this->name}.environment");
        $this->clientId = config("{$this->name}.client_id");
        $this->clientSecret = config("{$this->name}.client_secret");
        $this->baseUrl = config("{$this->name}.{$this->environment}_base_url");
    }

    /**
     * Setup with custom config
     */
    public function useCustomConfig(array $options): void
    {
        $this->environment = data_get($options, 'environment', '');
        $this->clientId = data_get($options, 'client_id', '');
        $this->clientSecret = data_get($options, 'client_secret', '');
        $this->baseUrl = data_get($options, "{$this->environment}_base_url", '');
    }

    /**
     * Get client id
     */
    public function clientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get client secret
     */
    public function clientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * Get base url
     */
    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get API url
     */
    public function url(string $endpoint, array $params = []): string
    {
        if (preg_match('/\./', $endpoint)) {
            $endpoint = config("{$this->name}.endpoints.{$endpoint}");
        }

        foreach ($params as $key => $value) {
            $endpoint = str_replace(":{$key}", $value, $endpoint);
        }

        return $this->baseUrl.$endpoint;
    }

    public static function instance(bool $reset = false)
    {
        if (! static::$instance || $reset) {
            static::$instance = new Config;
        }

        return static::$instance;
    }
}
