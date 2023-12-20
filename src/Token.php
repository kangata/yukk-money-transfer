<?php

namespace QuetzalStudio\YUKK\MoneyTransfer;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;

class Token
{
    protected static ?Token $instance = null;

    protected Config $config;

    protected Client $client;

    protected string $cacheName = 'yukk_money_transfer:auth';

    protected ?string $scope = null;

    public function __construct()
    {
        $this->config = MoneyTransfer::config();
        $this->client = MoneyTransfer::client();
    }

    public function config(Config $config): void
    {
        $this->config = $config;
    }

    public function client(Client $client): void
    {
        $this->client = $client;
    }

    public function cacheName(string $name): void
    {
        $this->cacheName = $name;
    }

    public function scope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function requestToken(): Response
    {
        $payload = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config->clientId(),
            'client_secret' => $this->config->clientSecret(),
        ];

        if ($this->scope) {
            $payload['scope'] = $this->scope;
        }

        $resp = $this->client->post(MoneyTransfer::url('/oauth/token'), $payload);

        if ($resp->ok()) {
            Cache::put($this->cacheName, $resp->json('result.access_token'), $resp->json('result.expires_in'));
        }

        return $resp;
    }

    public function get(): string
    {
        if (! Cache::has($this->cacheName)) {
            $this->requestToken();
        }

        return Cache::get($this->cacheName);
    }

    public static function instance(): static
    {
        if (! static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
