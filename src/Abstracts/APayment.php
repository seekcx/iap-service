<?php

namespace Simplephp\IapService\Abstracts;

use Simplephp\IapService\Contracts\INotify;
use Simplephp\IapService\Contracts\IPayment;
use Simplephp\IapService\Provider\Apple;

abstract class APayment implements IPayment
{
    /**
     *  版本
     */
    const VERSION = '1.0.0';

    /**
     * 正式环境
     */
    const ENV_PRODUCTION = 'production';

    /**
     * 沙箱环境
     */
    const ENV_SANDBOX = 'sandbox';

    /**
     * @var INotify[] $notifyCallbacks
     */
    protected $notifyCallbacks;

    /**
     * @var mixed
     */
    protected $payloadData;

    /**
     * 服务商名称
     */
    public function getSpName()
    {
        return static::SP_NAME;
    }

    /**
     * @param $path
     * @param array $queryParams
     * @return string
     */
    public function getFullRequestURL($path, ...$queryParams): string
    {
        return sprintf($path, ...$queryParams);
    }


    /**
     * 添加服务器通知回调，数组或者对象
     * @param array $notifyCallbacks
     * @return APayment|Apple
     */
    public function addServerNotifies(array $notifyCallbacks)
    {
        foreach ($notifyCallbacks as $notifyCallback) {
            $this->addServerNotify($notifyCallback);
        }
        return $this;
    }

    /**
     * 添加服务器通知回调
     * @param INotify $notifyCallback
     * @return void
     */
    public function addServerNotify(INotify $notifyCallback)
    {
        $notificationType = $notifyCallback->getNotificationType();
        if (!empty($notificationType)) {
            $subtype = $notifyCallback->getSubtype();
            $uniqueKey = $this->getNotifyUniqueKey($notificationType, $subtype);
            $this->notifyCallbacks[$uniqueKey] = $notifyCallback;
        }
    }

    /**
     * @param $notificationType
     * @param $subtype
     * @return INotify|null
     */
    public function getServerNotify($notificationType, $subtype = null): ?INotify
    {
        $uniqueKey = $this->getNotifyUniqueKey($notificationType, $subtype);
        return $this->notifyCallbacks[$uniqueKey] ?? null;
    }

    /**
     * @param $notificationType
     * @param $subtype
     * @return mixed|string
     */
    protected function getNotifyUniqueKey($notificationType = null, $subtype = null)
    {
        if (!empty($subtype)) {
            $notificationType .= '.' . $subtype;
        }
        return $notificationType;
    }

    /**
     * 获取异步通知数据
     * @return array
     */
    public function getNotifyData()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * 获取异步通知数据
     * @return mixed
     */
    public function getPayloadData()
    {
        return $this->payloadData;
    }

    /**
     * 获取版本
     * @return string
     */
    public function getVersion()
    {
        return static::VERSION;
    }
}