<?php

namespace QuetzalStudio\YUKK\MoneyTransfer;

use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Throwable;

class RequestLogger
{
    protected Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public static function dispatch(Response $response)
    {
        return new static($response);
    }

    /**
     * Get logger instance
     *
     * @return Logger|null
     */
    public function log()
    {
        if (is_null(MoneyTransfer::logChannel())) {
            return null;
        }

        return Log::channel(MoneyTransfer::logChannel());
    }

    /**
     * Get log message
     *
     * @return string
     */
    public function message(Request $request)
    {
        return implode(' ', [
            (string) $request->getMethod(),
            (string) $request->getUri(),
            $this->response->status(),
        ]);
    }

    /**
     * Get request context
     *
     * @return array
     */
    public function requestContext(Request $request)
    {
        $reqBody = json_decode($request->getBody(), true);
        $reqHeaders = $request->getHeaders();

        $request->getBody()->rewind();

        try {
            foreach (array_keys($reqBody) as $key) {
                if (in_array($key, ['client_secret'])) {
                    $reqBody[$key] = '**********';
                }
            }
        } catch (Throwable $e) {
            //
        }

        try {
            foreach (array_keys($reqHeaders) as $key) {
                if (in_array($key, ['Authorization'])) {
                    $reqHeaders[$key] = ['**********'];
                }
            }
        } catch (Throwable $e) {
            //
        }

        return [
            'body' => $reqBody,
            'headers' => $reqHeaders,
        ];
    }

    /**
     * Get response context
     *
     * @return array
     */
    public function responseContext()
    {
        $resBody = $this->response->json();

        try {
            foreach (array_keys($resBody['result'] ?? []) as $key) {
                if (in_array($key, ['access_token'])) {
                    $resBody['result'][$key] = '**********';
                }
            }
        } catch (Throwable $e) {
            //
        }

        return [
            'body' => $resBody,
            'headers' => $this->response->headers(),
        ];
    }

    public function __destruct()
    {
        if (! $this->log()) {
            return;
        }

        $request = $this->response->transferStats->getRequest();

        $this->log()->info($this->message($request), [
            'request' => $this->requestContext($request),
            'response' => $this->responseContext(),
        ]);
    }
}
