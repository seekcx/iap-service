<?php

namespace Simplephp\IapService;

use Simplephp\IapService\Provider\Apple;
use Simplephp\IapService\Provider\Huawei;
use Simplephp\IapService\Abstracts\APayment;

/**
 * Class Payment
 * @package Simplephp\PaymentSdk
 * @method static Huawei huawei(string $environment = APayment::ENV_PRODUCTION, string $merchant = 'default')
 * @method static Apple apple(string $environment = APayment::ENV_PRODUCTION, string $merchant = 'default')
 */
class Payment
{
    /**
     * @var $config
     */
    public $config = [];

    /**
     * 支付方式
     * @var array
     */
    const SUPPORT = [
        'huawei',
        'apple'
    ];

    /**
     * @param array $config
     * @return string
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $config
     * @return Payment
     */
    public static function config(array $config): Payment
    {
        return new self($config);
    }

    /**
     * @param string $provider
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $provider, array $arguments = [])
    {
        $provider = strtolower($provider);
        if (!in_array($provider, self::SUPPORT)) {
            throw new \InvalidArgumentException('不支持的支付方式');
        }
        $environment = $arguments[0] ?? APayment::ENV_PRODUCTION;
        $merchant = $arguments[1] ?? 'default';
        $config = $this->config[$provider][$merchant] ?? [];
        if (empty($config)) {
            throw new \InvalidArgumentException('支付方式配置信息不存在');
        }
        $class = __NAMESPACE__ . '\\Provider\\' . ucfirst($provider);
        return new $class($config, $environment);
    }
}