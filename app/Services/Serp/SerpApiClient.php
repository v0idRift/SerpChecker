<?php

namespace App\Services\Serp;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class SerpApiClient
{
    /**
     * @param  list<array<string, mixed>>  $payload
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload): array
    {
        $login = config('serp.login');
        $password = config('serp.password');

        if (! is_string($login) || $login === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException(
                'SERP API credentials are not set. Configure SERP_API_LOGIN and SERP_API_PASSWORD in .env.'
            );
        }

        $baseUrl = rtrim((string) config('serp.base_url', ''), '/');
        if ($baseUrl === '') {
            throw new RuntimeException('SERP API base URL is not set. Configure SERP_API_BASE_URL in .env.');
        }

        $timeout = (int) config('serp.timeout', 90);
        $connectTimeout = (int) config('serp.connect_timeout', 10);
        $retryTimes = (int) config('serp.retry.times', 2);
        $retrySleepMs = (int) config('serp.retry.sleep_ms', 250);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withBasicAuth($login, $password)
                ->baseUrl($baseUrl)
                ->withOptions([
                    RequestOptions::CONNECT_TIMEOUT => $connectTimeout,
                ])
                ->timeout($timeout)
                ->retry(max(0, $retryTimes), max(0, $retrySleepMs))
                ->post(ltrim($path, '/'), $payload);

            $response->throw();

            $json = $response->json();

            return is_array($json) ? $json : [];
        } catch (ConnectionException $e) {
            throw new RuntimeException('Unable to connect to the SERP API (connection error).', 0, $e);
        } catch (RequestException $e) {
            $message = 'SERP API request failed. HTTP '.$e->response->status().'.';

            throw new RuntimeException($message, 0, $e);
        }
    }
}
