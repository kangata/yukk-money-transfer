<?php

namespace QuetzalStudio\YUKK\MoneyTransfer;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Client
{
    protected ?PendingRequest $pendingRequest = null;

    protected bool $throwError;

    public function __construct(array $options = [])
    {
        $this->throwError = data_get($options, 'throw', true);
    }

    /**
     * Request with authorization token
     *
     * @return self
     */
    public function withAuth(): self
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . Token::instance()->get(),
        ]);
    }

    /**
     * Set idempotency key
     */
    public function idempotencyKey(string $key): self
    {
        return $this->withHeaders([
            'Idempotency-Key' => $key,
        ]);
    }

    public function __call($method, $arguments)
    {
        if (! $this->pendingRequest) {
            $this->pendingRequest = Http::acceptJson();
        }

        if ($method == 'withHeaders') {
            $this->pendingRequest->withHeaders(...$arguments);

            return $this;
        }

        if (in_array($method, ['get', 'post', 'put'])) {
            $response = $this->pendingRequest->$method(...$arguments);

            RequestLogger::dispatch($response);

            $this->pendingRequest = null;

            if ($this->throwError) {
                $response = $response->throw();
            }

            return $response;
        }

        $this->pendingRequest->$method(...$arguments);

        return $this;
    }
}
