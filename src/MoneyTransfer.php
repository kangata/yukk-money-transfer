<?php

namespace QuetzalStudio\YUKK\MoneyTransfer;

use Illuminate\Http\Client\Response;
use QuetzalStudio\YUKK\MoneyTransfer\Models\BankAccountInquiry;
use QuetzalStudio\YUKK\MoneyTransfer\Models\Disbursement;

class MoneyTransfer
{
    protected static ?Config $config = null;

    protected static ?Client $client = null;

    protected static bool $httpResponse = false;

    protected static bool $throw = true;

    protected static ?string $logChannel = null;

    protected static array $configOptions = [];

    public static function useHttpResponse(): void
    {
        static::$httpResponse = true;
    }

    public static function disableThrow(): void
    {
        static::$throw = false;
    }

    public static function useLogger(string $channel): void
    {
        static::$logChannel = $channel;
    }

    public static function logChannel(): ?string
    {
        return static::$logChannel;
    }

    public static function config(): Config
    {
        if (! static::$config) {
            static::$config = Config::instance();
        }

        return static::$config;
    }

    public static function client(): Client
    {
        $options = [
            'throw' => static::$throw,
        ];

        if (! static::$client) {
            static::$client = new Client($options);
        }

        return static::$client;
    }

    public static function url(string $endpoint, array $params = []): string
    {
        return static::config()->url($endpoint, $params);
    }

    public static function handleResponse(Response $response, ?string $key = null): mixed
    {
        return static::$httpResponse ? $response : $response->json($key);
    }

    public static function balance()
    {
        $resp = static::client()->withAuth()->get(static::url('v2.get_balance'));

        return static::handleResponse($resp, 'result');
    }

    public static function banks()
    {
        $resp = static::client()->withAuth()->get(static::url('v2.get_banks'));

        return static::handleResponse($resp, 'result');
    }

    public static function bankAccountInquiry(BankAccountInquiry $payload)
    {
        $resp = static::client()
            ->withAuth()
            ->post(static::url('v2.bank_account_inquiry'), $payload->toArray());

        return static::handleResponse($resp, 'result');
    }

    public static function createDisbursement(Disbursement $payload, string $idempotencyKey)
    {
        $resp = static::client()
            ->withAuth()
            ->idempotencyKey($idempotencyKey)
            ->post(static::url('v2.create_disbursement'), $payload->toArray());

        return static::handleResponse($resp, 'result');
    }

    public static function findDisbursement(string $code, string $field = null)
    {
        $query = $field ? compact('field') : [];

        $resp = static::client()
            ->withAuth()
            ->withQueryParameters($query)
            ->get(static::url('v2.find_disbursement', ['code' => $code]));

        return static::handleResponse($resp, 'result');
    }
}
