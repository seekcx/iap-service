<?php
/**
 * 事件通知示例
 * @file examples/huawei_server_notify.php
 */

use Simplephp\IapService\Abstracts\APayment;
use Simplephp\IapService\Abstracts\APayNotify;
use Simplephp\IapService\Payment;

require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');

$config = [
    'huawei' => [
        'default' => [
            'kid' => '7f93***c96f',// 密钥ID
            'aid' => '5763**4467',             // 应用ID
            'bundle_id' => 'com.***.bundler',  // Bundle ID
            'issuer_id' => '0d69***21c1', // Issuer ID
            'leaf_cert_oid' => '1.3.6.***.1.1', // 叶子证书OID
            'private_key_path' => __DIR__ . '/cert/IAPKey_****4c96f.p8',
        ]
    ]
];

/****************************************************
 * Huawei-IAP 消耗型/非消耗型商品购买成功或自动续期订阅商品的第一次购买成功
 * @link huawei_iap_readme.md
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-key-event-notifications-V5#section526717832913
 ****************************************************/
class DidNewTransactionInitialBuyNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_NEW_TRANSACTION';

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

/****************************************************
 * Apple IAP 续期成功通知
 * @link apple_storekit2_readme.md
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-key-event-notifications-V5#section526717832913
 ****************************************************/
class DidNewTransactionDidRenewNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_NEW_TRANSACTION';

    /**
     * @return string
     */
    public $subtype = 'DID_RENEW';

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
 * Apple IAP 用户主动恢复了一个订阅型商品，续期恢复正常
 * @link apple_storekit2_readme.md
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-key-event-notifications-V5#section526717832913
 */
class DidNewTransactionRestoreNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_NEW_TRANSACTION';

    /**
     * @return string
     */
    public $subtype = 'RESTORE';

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
 * Apple IAP 自动续期功能开启
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 */
class DidChangeRenewalStatusAutoRenewEnabledNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_CHANGE_RENEWAL_STATUS';

    /**
     * @return string
     */
    public $subtype = 'AUTO_RENEW_ENABLED';

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
 * Apple IAP 自动续期功能关闭
 * @link apple_storekit2_readme.md
 * @link https://developer.apple.com/documentation/appstoreservernotifications/notificationtype
 */
class DidChangeRenewalStatusAutoRenewDisabledNotify extends APayNotify
{
    /**
     * @return string
     */
    public $notificationType = 'DID_CHANGE_RENEWAL_STATUS';

    /**
     * @return string
     */
    public $subtype = 'AUTO_RENEW_DISABLED';

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

try {
    $huaweiIAPService = Payment::config($config)->huawei(APayment::ENV_SANDBOX);
    $notifyData = $huaweiIAPService->getNotifyData();
    // ⚠️：请根据实际业务需求处理需要的通知事件
    // 继承 APayNotify 类，添加对应 $notificationType(通知主类型) ，$subtype(通知子类型) 属性,实现 handle 方法，处理通知事件
    $huaweiIAPService->addServerNotifies([
        new DidNewTransactionInitialBuyNotify(),
        new DidNewTransactionDidRenewNotify(),
        new DidNewTransactionRestoreNotify(),
        new DidChangeRenewalStatusAutoRenewEnabledNotify(),
        new DidChangeRenewalStatusAutoRenewDisabledNotify(),
    ])->notify();
} catch (\InvalidArgumentException $e) {
    echo '参数：' . $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}