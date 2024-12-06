<?php

namespace Simplephp\IapService\Model;

class NotificationPayload extends BaseModel
{
    /**
     * @var string $notificationType 通知主类型
     */
    public $notificationType;
    /**
     * @var string $notificationSubtype 通知子类型
     */
    public $notificationSubtype;
    /**
     * @var string $notificationRequestId 通知唯一请求ID
     */
    public $notificationRequestId;
    /**
     * @var notificationMetaData $notificationMetaData 通知元数据
     */
    public $notificationMetaData;
    /**
     * @var string $notificationVersion 通知版本
     */
    public $notificationVersion;
    /**
     * @var int $signedTime 通知签名时间
     */
    public $signedTime;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->notificationMetaData = new notificationMetaData((array)$this->notificationMetaData);
    }
}