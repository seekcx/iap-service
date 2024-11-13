<?php
/**
 * 事件通知示例
 * @file examples/app_store_notify.php
 */
use Simplephp\IapService\Abstracts\APayment;
use Simplephp\IapService\Abstracts\APayNotify;
use Simplephp\IapService\Payment;

require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');

$config = [
    'apple' => [
        'default' => [
            'leaf_cert_oid' => '1.2.***.6.11.1',
            'kid' => 'D37***2LT',
            'bundle_id' => 'com.****.bundler',
            'issuer_id' => '69a6-***-a4d1',
            'private_key_path' => __DIR__ . '/cert/SubscriptionKey_****.p8',
        ]
    ],
];

/****************************************************
 * Apple-IAP 用户首次订阅通知
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 ****************************************************/
class SubscribedInitialBuyNotify extends APayNotify
{
    /**
     * @return string
     */
    public  $notificationType = 'SUBSCRIBED';

    /**
     * @return string
     */
    public  $subtype = 'INITIAL_BUY';

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return false
     */
    public function handle(string $serviceProvider, array $notifyData)
    {
        return false;
    }
}

/****************************************************
 * Apple-IAP 用户重新订阅通知
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 ****************************************************/
class SubscribedResubscribeNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'SUBSCRIBED';

    /**
     * @return string
     */
    public $subtype = 'INITIAL_BUY';

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return false
     */
    public function handle(string $serviceProvider, array $notifyData)
    {
        return false;
    }
}

/**
 * Apple-IAP 自动续订订阅成功通知
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 */
class DidRenewNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_RENEW';

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return false
     */
    public function handle(string $serviceProvider, array $notifyData)
    {
        return false;
    }
}


/**
 * Apple-IAP 使用兑换码成功订阅通知
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 */
class OfferRedeemedInitialBuyNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'OFFER_REDEEMED';

    /**
     * @return string
     */
    public $subtype = 'INITIAL_BUY';

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return false
     */
    public function handle(string $serviceProvider, array $notifyData)
    {
        return false;
    }
}

/**
 * Apple-IAP 客户购买消耗品、非消耗品或非续订订阅的通知
 * @link apple_storekit2_readme.md
 */
class OneTimeChargeNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'ONE_TIME_CHARGE';

    /**
     * @param string $serviceProvider
     * @param array $notifyData
     * @return false
     */
    public function handle(string $serviceProvider, array $notifyData)
    {
        return false;
    }
}

/**
 * 通知事件包括以下几大类（根据业务接入）：
 * 1、订阅事件（包括初始订阅、重新订阅和成功自动续订）
 * 2、更改订阅事件(包括升级、降级或取消)
 * 3、使用兑换码事件(使用兑换码/优惠码初始订阅、使用兑换码/优惠码重新订阅，使用兑换码/优惠码升/降级)
 * 4、计费事件（包括计费重试、进入和退出计费宽限期以及订阅到期）
 * 5、自动续订订阅的价格时发生的事件或通知
 * 6、用户申请退款或取消家庭共享
 * 7、开发者请求延长订阅续订日期
 */
try {
    $appleIAPService = Payment::config($config)->apple(APayment::ENV_SANDBOX);
    $notifyData = $appleIAPService->getNotifyData();
    // ⚠️：请根据实际业务需求处理需要的通知事件
    // 继承 APayNotify 类，添加对应 $notificationType(通知主类型) ，$subtype(通知子类型) 属性,实现 handle 方法，处理通知事件
    $appleIAPService->addServerNotifies([
        new SubscribedInitialBuyNotify(),
        new SubscribedResubscribeNotify(),
        new DidRenewNotify(),
        new OfferRedeemedInitialBuyNotify(),
        new OneTimeChargeNotify(),
    ])->notify();
} catch (\InvalidArgumentException $e) {
    echo '参数：' . $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}