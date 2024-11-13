<?php

namespace Simplephp\IapService\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Simplephp\IapService\Middleware\GuzzleRetryMiddleware;
use Simplephp\IapService\Middleware\JwtAuthorizationMiddleware;

/**
 * 基于 GuzzleHttp 的请求类
 * Class Request
 * @package Simplephp\IapService\Util
 */
class Request
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var float|mixed
     */
    private $timeout;

    /**
     * Request constructor.
     * @param array $options
     */
    private function __construct(array $options = [])
    {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory($options));
        $stack->push(JwtAuthorizationMiddleware::factory($options));
        $middlewares = $options['middlewares'] ?? [];
        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                if (is_callable($middleware)) {
                    $stack->push($middleware);
                }
            }
        }
        $baseUri = $options['base_uri'] ?? '';
        $this->timeout = $options['timeout'] ?? 3.0;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => $this->timeout,
            'handler' => $stack,
        ]);
    }

    /**
     * @param array $options
     * @return static
     */
    public static function factory(array $options = []): self
    {
        return new self($options);
    }

    /**
     * GET 请求
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function get(string $url, array $options = [], array $guzzleOptions = [])
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->request('GET', $url, $options);
    }

    /**
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return PromiseInterface
     */
    public function getAsync(string $url, array $options = [], array $guzzleOptions = []): PromiseInterface
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->requestAsync('GET', $url, $options);
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function post(string $url, array $options = [], array $guzzleOptions = [])
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->request('POST', $url, $options);
    }

    /**
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return PromiseInterface
     */
    public function postAsync(string $url, array $options = [], array $guzzleOptions = []): PromiseInterface
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->requestAsync('POST', $url, $options);
    }

    /**
     * PUT 请求
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function put(string $url, array $options = [], array $guzzleOptions = [])
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->request('PUT', $url, $options);
    }

    /**
     * @param string $url
     * @param array $options
     * @param array $guzzleOptions
     * @return PromiseInterface
     */
    public function putAsync(string $url, array $options = [], array $guzzleOptions = []): PromiseInterface
    {
        if (!empty($guzzleOptions)) {
            $options = array_merge($options, $guzzleOptions);
        }
        return $this->client->requestAsync('PUT', $url, $options);
    }
}