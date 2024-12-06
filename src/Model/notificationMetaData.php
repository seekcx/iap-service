<?php

namespace Simplephp\IapService\Model;

class notificationMetaData extends BaseModel
{
    /**
     * @var string $environment 环境类型。
     */
    public $environment;
    /**
     * @var string $applicationId 应用Id。
     */
    public $applicationId;
    /**
     * @var string $packageName 应用包名。
     */
    public $packageName;
    /**
     * @var int $type 商品类型。
     */
    public $type;
    /**
     * @var string $currentProductId 最近一个有效订阅的商品ID。仅自动续期订阅商品场景下存在值。
     */
    public $currentProductId;
    /**
     * @var string $subGroupId 订阅组ID。仅自动续期订阅商品场景下存在值。
     */
    public $subGroupId;
    /**
     * @var string $subGroupGenerationId 订阅组的代ID。
     */
    public $subGroupGenerationId;
    /**
     * @var string $subscriptionId 商品的订阅ID。
     */
    public $subscriptionId;
    /**
     * @var string $purchaseToken 商品的购买Token，发起购买和查询订阅信息均会返回。
     */
    public $purchaseToken;
    /**
     * @var string $purchaseOrderId 具体一笔订单中对应的购买订单号。
     */
    public $purchaseOrderId;

}