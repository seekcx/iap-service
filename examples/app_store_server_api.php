<?php
/**
 * App Store Server API
 * @file examples/app_store_server_api.php
 */

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Simplephp\IapService\Abstracts\APayment;
use Simplephp\IapService\Exception\VerificationException;
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
$testOneByOne = false;
/****************************************************
 * 注意处理异常：
 * 1. InvalidArgumentException 入参异常错误
 * 2. VerificationException 验证异常-JWT验证失败
 * 3. ConnectException 网络连接异常-网络连接失败
 * 4. RequestException 请求第三方返回异常-请求失败
 * 5. Exception 其他异常
 * notificationType 通知类型
 * Endpoint                            Rate limit (per second)
 * Get Transaction Info                50
 * Get Transaction History             50
 * Get Transaction History V1          50
 * Get All Subscription Statuses       50
 * Send Consumption Information        50
 * Get Notification History            50
 * Extend a Subscription Renewal Date  20
 * Look Up Order ID                    10
 * Get Refund History                  10
 * Get Refund History V1               10
 * Extend Subscription Renewal Dates for All Active Subscribers    1
 * Request a Test Notification         1
 * Get Test Notification Status        1
 ****************************************************/
/****************************************************
 * 华为IAP-通知数据解码测试
 * @link
 ****************************************************/
try {
    $jws = 'eyJhbGciOiJFUzI1NiIsIng1YyI6WyJNSUlFTURDQ0E3YWdBd0lCQWdJUWZUbGZkMGZOdkZXdnpDMVlJQU5zWGpBS0JnZ3Foa2pPUFFRREF6QjFNVVF3UWdZRFZRUURERHRCY0hCc1pTQlhiM0pzWkhkcFpHVWdSR1YyWld4dmNHVnlJRkpsYkdGMGFXOXVjeUJEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURUxNQWtHQTFVRUN3d0NSell4RXpBUkJnTlZCQW9NQ2tGd2NHeGxJRWx1WXk0eEN6QUpCZ05WQkFZVEFsVlRNQjRYRFRJek1Ea3hNakU1TlRFMU0xb1hEVEkxTVRBeE1URTVOVEUxTWxvd2daSXhRREErQmdOVkJBTU1OMUJ5YjJRZ1JVTkRJRTFoWXlCQmNIQWdVM1J2Y21VZ1lXNWtJR2xVZFc1bGN5QlRkRzl5WlNCU1pXTmxhWEIwSUZOcFoyNXBibWN4TERBcUJnTlZCQXNNSTBGd2NHeGxJRmR2Y214a2QybGtaU0JFWlhabGJHOXdaWElnVW1Wc1lYUnBiMjV6TVJNd0VRWURWUVFLREFwQmNIQnNaU0JKYm1NdU1Rc3dDUVlEVlFRR0V3SlZVekJaTUJNR0J5cUdTTTQ5QWdFR0NDcUdTTTQ5QXdFSEEwSUFCRUZFWWUvSnFUcXlRdi9kdFhrYXVESENTY1YxMjlGWVJWLzB4aUIyNG5DUWt6UWYzYXNISk9OUjVyMFJBMGFMdko0MzJoeTFTWk1vdXZ5ZnBtMjZqWFNqZ2dJSU1JSUNCREFNQmdOVkhSTUJBZjhFQWpBQU1COEdBMVVkSXdRWU1CYUFGRDh2bENOUjAxREptaWc5N2JCODVjK2xrR0taTUhBR0NDc0dBUVVGQndFQkJHUXdZakF0QmdnckJnRUZCUWN3QW9ZaGFIUjBjRG92TDJObGNuUnpMbUZ3Y0d4bExtTnZiUzkzZDJSeVp6WXVaR1Z5TURFR0NDc0dBUVVGQnpBQmhpVm9kSFJ3T2k4dmIyTnpjQzVoY0hCc1pTNWpiMjB2YjJOemNEQXpMWGQzWkhKbk5qQXlNSUlCSGdZRFZSMGdCSUlCRlRDQ0FSRXdnZ0VOQmdvcWhraUc5Mk5rQlFZQk1JSCtNSUhEQmdnckJnRUZCUWNDQWpDQnRneUJzMUpsYkdsaGJtTmxJRzl1SUhSb2FYTWdZMlZ5ZEdsbWFXTmhkR1VnWW5rZ1lXNTVJSEJoY25SNUlHRnpjM1Z0WlhNZ1lXTmpaWEIwWVc1alpTQnZaaUIwYUdVZ2RHaGxiaUJoY0hCc2FXTmhZbXhsSUhOMFlXNWtZWEprSUhSbGNtMXpJR0Z1WkNCamIyNWthWFJwYjI1eklHOW1JSFZ6WlN3Z1kyVnlkR2xtYVdOaGRHVWdjRzlzYVdONUlHRnVaQ0JqWlhKMGFXWnBZMkYwYVc5dUlIQnlZV04wYVdObElITjBZWFJsYldWdWRITXVNRFlHQ0NzR0FRVUZCd0lCRmlwb2RIUndPaTh2ZDNkM0xtRndjR3hsTG1OdmJTOWpaWEowYVdacFkyRjBaV0YxZEdodmNtbDBlUzh3SFFZRFZSME9CQllFRkFNczhQanM2VmhXR1FsekUyWk9FK0dYNE9vL01BNEdBMVVkRHdFQi93UUVBd0lIZ0RBUUJnb3Foa2lHOTJOa0Jnc0JCQUlGQURBS0JnZ3Foa2pPUFFRREF3Tm9BREJsQWpFQTh5Uk5kc2twNTA2REZkUExnaExMSndBdjVKOGhCR0xhSThERXhkY1BYK2FCS2pqTzhlVW85S3BmcGNOWVVZNVlBakFQWG1NWEVaTCtRMDJhZHJtbXNoTnh6M05uS20rb3VRd1U3dkJUbjBMdmxNN3ZwczJZc2xWVGFtUllMNGFTczVrPSIsIk1JSURGakNDQXB5Z0F3SUJBZ0lVSXNHaFJ3cDBjMm52VTRZU3ljYWZQVGp6Yk5jd0NnWUlLb1pJemowRUF3TXdaekViTUJrR0ExVUVBd3dTUVhCd2JHVWdVbTl2ZENCRFFTQXRJRWN6TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd0hoY05NakV3TXpFM01qQXpOekV3V2hjTk16WXdNekU1TURBd01EQXdXakIxTVVRd1FnWURWUVFERER0QmNIQnNaU0JYYjNKc1pIZHBaR1VnUkdWMlpXeHZjR1Z5SUZKbGJHRjBhVzl1Y3lCRFpYSjBhV1pwWTJGMGFXOXVJRUYxZEdodmNtbDBlVEVMTUFrR0ExVUVDd3dDUnpZeEV6QVJCZ05WQkFvTUNrRndjR3hsSUVsdVl5NHhDekFKQmdOVkJBWVRBbFZUTUhZd0VBWUhLb1pJemowQ0FRWUZLNEVFQUNJRFlnQUVic1FLQzk0UHJsV21aWG5YZ3R4emRWSkw4VDBTR1luZ0RSR3BuZ24zTjZQVDhKTUViN0ZEaTRiQm1QaENuWjMvc3E2UEYvY0djS1hXc0w1dk90ZVJoeUo0NXgzQVNQN2NPQithYW85MGZjcHhTdi9FWkZibmlBYk5nWkdoSWhwSW80SDZNSUgzTUJJR0ExVWRFd0VCL3dRSU1BWUJBZjhDQVFBd0h3WURWUjBqQkJnd0ZvQVV1N0Rlb1ZnemlKcWtpcG5ldnIzcnI5ckxKS3N3UmdZSUt3WUJCUVVIQVFFRU9qQTRNRFlHQ0NzR0FRVUZCekFCaGlwb2RIUndPaTh2YjJOemNDNWhjSEJzWlM1amIyMHZiMk56Y0RBekxXRndjR3hsY205dmRHTmhaek13TndZRFZSMGZCREF3TGpBc29DcWdLSVltYUhSMGNEb3ZMMk55YkM1aGNIQnNaUzVqYjIwdllYQndiR1Z5YjI5MFkyRm5NeTVqY213d0hRWURWUjBPQkJZRUZEOHZsQ05SMDFESm1pZzk3YkI4NWMrbGtHS1pNQTRHQTFVZER3RUIvd1FFQXdJQkJqQVFCZ29xaGtpRzkyTmtCZ0lCQkFJRkFEQUtCZ2dxaGtqT1BRUURBd05vQURCbEFqQkFYaFNxNUl5S29nTUNQdHc0OTBCYUI2NzdDYUVHSlh1ZlFCL0VxWkdkNkNTamlDdE9udU1UYlhWWG14eGN4ZmtDTVFEVFNQeGFyWlh2TnJreFUzVGtVTUkzM3l6dkZWVlJUNHd4V0pDOTk0T3NkY1o0K1JHTnNZRHlSNWdtZHIwbkRHZz0iLCJNSUlDUXpDQ0FjbWdBd0lCQWdJSUxjWDhpTkxGUzVVd0NnWUlLb1pJemowRUF3TXdaekViTUJrR0ExVUVBd3dTUVhCd2JHVWdVbTl2ZENCRFFTQXRJRWN6TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd0hoY05NVFF3TkRNd01UZ3hPVEEyV2hjTk16a3dORE13TVRneE9UQTJXakJuTVJzd0dRWURWUVFEREJKQmNIQnNaU0JTYjI5MElFTkJJQzBnUnpNeEpqQWtCZ05WQkFzTUhVRndjR3hsSUVObGNuUnBabWxqWVhScGIyNGdRWFYwYUc5eWFYUjVNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVFzd0NRWURWUVFHRXdKVlV6QjJNQkFHQnlxR1NNNDlBZ0VHQlN1QkJBQWlBMklBQkpqcEx6MUFjcVR0a3lKeWdSTWMzUkNWOGNXalRuSGNGQmJaRHVXbUJTcDNaSHRmVGpqVHV4eEV0WC8xSDdZeVlsM0o2WVJiVHpCUEVWb0EvVmhZREtYMUR5eE5CMGNUZGRxWGw1ZHZNVnp0SzUxN0lEdll1VlRaWHBta09sRUtNYU5DTUVBd0hRWURWUjBPQkJZRUZMdXczcUZZTTRpYXBJcVozcjY5NjYvYXl5U3JNQThHQTFVZEV3RUIvd1FGTUFNQkFmOHdEZ1lEVlIwUEFRSC9CQVFEQWdFR01Bb0dDQ3FHU000OUJBTURBMmdBTUdVQ01RQ0Q2Y0hFRmw0YVhUUVkyZTN2OUd3T0FFWkx1Tit5UmhIRkQvM21lb3locG12T3dnUFVuUFdUeG5TNGF0K3FJeFVDTUcxbWloREsxQTNVVDgyTlF6NjBpbU9sTTI3amJkb1h0MlFmeUZNbStZaGlkRGtMRjF2TFVhZ002QmdENTZLeUtBPT0iXX0.eyJub3RpZmljYXRpb25UeXBlIjoiVEVTVCIsIm5vdGlmaWNhdGlvblVVSUQiOiIyZTJhNmNlMC1lYzlmLTQwMzctYjFiZi02MThhZjg1NTI3YmQiLCJkYXRhIjp7ImFwcEFwcGxlSWQiOjY0NTAyODQ4NzIsImJ1bmRsZUlkIjoiY29tLm5pbmV0b24udGFsbGVyIiwiZW52aXJvbm1lbnQiOiJQcm9kdWN0aW9uIn0sInZlcnNpb24iOiIyLjAiLCJzaWduZWREYXRlIjoxNzI5MTM2NjUxMTI4fQ.9Lx1xURZbPDookrFeNCopt1gTHTT6jyZMOyfmxpiZeK9bhucvO7k6DWzwBSrVYkeNvgchHdJPFvX8be0A45aog';
    $result = Payment::config($config)->apple(APayment::ENV_SANDBOX)->getJwtService()->decodedSignedData($jws);
    var_dump($result);
} catch (VerificationException|\Exception $e) {
    echo $e->getMessage();
}

/****************************************************
 * 苹果IAP-通过transactionId/originalTransactionId获取交易信息
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_transaction_info
 ****************************************************/
try {
    $transactionId = '210001867817339';
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->transactions($transactionId);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 苹果IAP-通过订单编号获取交易信息
 * @link https://developer.apple.com/documentation/appstoreserverapi/look_up_order_id
 ****************************************************/
try {
    $orderId = 'MM9LDJZXXZ';
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->lookup($orderId);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 苹果IAP-通过originalTransactionId获取用户在APP内所有的订阅状态
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_transaction_info
 ****************************************************/
try {
    $originalTransactionId = '210001867817339';
    /**
     * 1 自动续订处于激活状态。
     * 2 自动续订已过期。
     * 3 自动续订服务处于账单重试期。
     * 4 自动续订服务处于账单宽限期。
     * 5 自动续订已撤销。App Store 已从家庭共享中退款或撤销交易。
     */
    $queryParams = [
        'status' => 1,
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->subscriptionStatuses($originalTransactionId, $queryParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 苹果IAP-通过originalTransactionId获取用户在APP内所有的订阅状态
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_transaction_info
 ****************************************************/
try {
    $originalTransactionId = '210001867817339';
    /**
     * 1 自动续订处于激活状态。
     * 2 自动续订已过期。
     * 3 自动续订服务处于账单重试期。
     * 4 自动续订服务处于账单宽限期。
     * 5 自动续订已撤销。App Store 已从家庭共享中退款或撤销交易。
     */
    $queryParams = [
        //'revision' => '', // 游标，第一次请求不传，后续请求传上一次返回的revision字段
        'startDate' => 1727777780000, // 交易开始日期，毫秒时间戳
        'endDate' => 1730456180000, // 交易结束日期，毫秒时间戳
        'productId' => [
            'com.example.product1',
            'com.example.product2'
        ],
        'productType' => [
            'AUTO_RENEWABLE',
            'NON_RENEWING',
            'CONSUMABLE',
            'NON_CONSUMABLE'
        ],
        'inAppOwnershipType' => '', // PURCHASED：用户购买，FAMILY_SHARED：家庭共享
        'sort' => 'ASCENDING', // ASCENDING：升序，DESCENDING：降序
        'revoked' => 'true', // true：已撤销，false：未撤销
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->transactionHistory($originalTransactionId, $queryParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 苹果IAP-通过originalTransactionId获取用户在APP内所有的订阅状态
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_transaction_info
 ****************************************************/
try {
    $originalTransactionId = '210001867817339';
    /**
     * 1 自动续订处于激活状态。
     * 2 自动续订已过期。
     * 3 自动续订服务处于账单重试期。
     * 4 自动续订服务处于账单宽限期。
     * 5 自动续订已撤销。App Store 已从家庭共享中退款或撤销交易。
     */
    $queryParams = [
        'revision' => '', // 游标，第一次请求不传，后续请求传上一次返回的revision字段
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->refundHistory($originalTransactionId, $queryParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 通过originalTransactionId延长用户订阅有效期
 * @link https://developer.apple.com/documentation/appstoreserverapi/extend_a_subscription_renewal_date
 * {PUT} /inApps/v1/subscriptions/extend/{originalTransactionId}
 ****************************************************/
try {
    $originalTransactionId = '2100018678173391';
    $putParams = [
        'requestIdentifier' => time() . rand(10000, 99999), // 请求标识符，用于标识请求的唯一性
        'extendByDays' => 1, // 必须是 1-90 天之间
        'extendReasonCode' => 1, // 0 未申报 1 让客户满意 2 其他原因 3 服务商问题或中断
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->extendSubscription($originalTransactionId, $putParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 根据条件批量延长符合条件用户订阅有效期
 * @link https://developer.apple.com/documentation/appstoreserverapi/extend_subscription_renewal_dates_for_all_active_subscribers
 ****************************************************/
try {
    $postParams = [
        'requestIdentifier' => time() . rand(10000, 99999), // 请求标识符，用于标识请求的唯一性
        'extendByDays' => 1, // 必须是 1-90 天之间
        'extendReasonCode' => 1, // 0 未申报 1 让客户满意 2 其他原因 3 服务商问题或中断
        'productId' => 'com.example.product1', // 产品ID
        'storefrontCountryCodes' => ['CN'], // 国家代码
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->extendSubscriptionMass($postParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 防欺诈信息提交，当用户申请退款时，苹果通知（CONSUMPTION_REQUEST）开发者服务器，开发者可在12小时内，提供用户的信息（比如游戏金币是否已消费、用户充值过多少钱、退款过多少钱等），最后苹果收到这些信息，协助“退款决策系统” 来决定是否允许用户退款。
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_refund_history
 * 参数讲解移步至：https://developer.apple.com/documentation/appstoreserverapi/consumptionrequest#properties
 ****************************************************/
try {
    $originalTransactionId = '2100018678173391';
    $putParams = [
        // ... 请参考链接中的参数说明
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->sendConsumptionInformation($originalTransactionId, $putParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 获取APP Store 通知服务器向开发者发送的历史通知记录
 * @kink https://developer.apple.com/documentation/appstoreserverapi/get_notification_history
 ****************************************************/
try {
    $paginationToken = ''; // 分页标识符,第一次请求不需要传递，后续请求需要传递上一次请求返回的值
    $postParams = [
        'startDate' => 1727777780000, // 交易开始日期，毫秒时间戳
        'endDate' => 1730456180000, // 交易结束日期，毫秒时间戳
        /**
         * 通知类型
         * SUBSCRIBED                           用户订阅了产品
         * DID_CHANGE_RENEWAL_PREF              用户更改了订阅的自动续订首选项
         * DID_CHANGE_RENEWAL_STATUS            用户更改了订阅的自动续订状态
         * OFFER_REDEEMED                       用户兑换了优惠
         * DID_RENEW                            用户的订阅已续订
         * EXPIRED                              用户的订阅已过期
         * DID_FAIL_TO_RENEW                    用户的订阅未能续订
         * GRACE_PERIOD_EXPIRED                 用户的宽限期已过期
         * PRICE_INCREASE                       产品价格已上涨
         * REFUND                               用户收到了退款
         * REFUND_DECLINED                      用户的退款被拒绝
         * CONSUMPTION_REQUEST                  用户请求退款
         * RENEWAL_EXTENDED                     用户的订阅已延长
         * REVOKE                               用户的订阅已撤销
         * TEST                                 用于测试的通知
         * RENEWAL_EXTENSION                    用户的订阅已延长
         * REFUND_REVERSED                      用户的退款已撤销
         * EXTERNAL_PURCHASE_TOKEN              用户的订阅已转移到外部购买令牌
         * ONE_TIME_CHARGE                      用户购买了一次性产品
         */
        'notificationType' => [
            'TEST',
        ],
        'onlyFailures' => 'true', // true：仅返回失败的通知，false：返回所有通知
        //'transactionId' => '1000000000000000', // notificationType 和 transactionId 二选一不能同时存在
    ];
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->notificationHistory($originalTransactionId, $putParams);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * 苹果IAP-向App Store通知服务器发送测试通知到开发者服务器
 * @link https://developer.apple.com/documentation/appstoreserverapi/request_a_test_notification
 ****************************************************/
try {
    /**
     * 通知类型
     * SUBSCRIBED                           用户订阅了产品
     * DID_CHANGE_RENEWAL_PREF              用户更改了订阅的自动续订首选项
     * DID_CHANGE_RENEWAL_STATUS            用户更改了订阅的自动续订状态
     * OFFER_REDEEMED                       用户兑换了优惠
     * DID_RENEW                            用户的订阅已续订
     * EXPIRED                              用户的订阅已过期
     * DID_FAIL_TO_RENEW                    用户的订阅未能续订
     * GRACE_PERIOD_EXPIRED                 用户的宽限期已过期
     * PRICE_INCREASE                       产品价格已上涨
     * REFUND                               用户收到了退款
     * REFUND_DECLINED                      用户的退款被拒绝
     * CONSUMPTION_REQUEST                  用户请求退款
     * RENEWAL_EXTENDED                     用户的订阅已延长
     * REVOKE                               用户的订阅已撤销
     * TEST                                 用于测试的通知
     * RENEWAL_EXTENSION                    用户的订阅已延长
     * REFUND_REVERSED                      用户的退款已撤销
     * EXTERNAL_PURCHASE_TOKEN              用户的订阅已转移到外部购买令牌
     * ONE_TIME_CHARGE                      用户购买了一次性产品
     */
    $notificationType = 'TEST';
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->notificationTest($notificationType);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        $testNotificationToken = $result['testNotificationToken'] ?? '';
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}

/****************************************************
 * App Store 通知服务器向开发者服务器发送的测试通知结果检测
 * @link https://developer.apple.com/documentation/appstoreserverapi/get_test_notification_status
 ****************************************************/
try {
    $testNotificationToken = "da42d1a4-6a96-4a1d-9f26-ac1c2801c435_1730877553872";
    $appleIAPService = Payment::config($config)->apple();
    $response = $appleIAPService->notificationStatus($testNotificationToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        // status 0 您在查找订单 ID请求中提供的内容有效，且包含至少一项应用内购买 1 无效或不包含您的应用的任何应用内购买
        var_dump($result);
    } else {
        echo $response->getMsg();
    }
} catch (\InvalidArgumentException $e) {
    echo '入参数校验异常：' . $e->getMessage();
} catch (ConnectException $e) {
    echo '网络连接异常：' . $e->getMessage();
} catch (RequestException $e) {
    echo '请求服务端异常：' . $e->getMessage();
} catch (VerificationException $e) {
    echo '数据校验异常：' . $e->getMessage();
} catch (\Exception $e) {
    echo '其他异常：' . $e->getMessage();
}