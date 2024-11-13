<?php
/**
 * Huawei Server API
 * @file examples/huawei_server_api.php
 */

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Simplephp\IapService\Abstracts\APayment;
use Simplephp\IapService\Exception\VerificationException;
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
$testOneByOne = false;
/****************************************************
 * 注意处理异常：
 * 1. InvalidArgumentException 入参异常错误
 * 2. VerificationException 验证异常-JWT验证失败
 * 3. ConnectException 网络连接异常-网络连接失败
 * 4. RequestException 请求第三方返回异常-请求失败
 * 5. Exception 其他异常
 ****************************************************/

/****************************************************
 * 华为IAP-通知数据解码测试
 * @link
 ****************************************************/
try {
    $jws = 'eyJhbGciOiJFUzI1NiIsIng1YyI6WyJNSUlFTURDQ0E3YWdBd0lCQWdJUWZUbGZkMGZOdkZXdnpDMVlJQU5zWGpBS0JnZ3Foa2pPUFFRREF6QjFNVVF3UWdZRFZRUURERHRCY0hCc1pTQlhiM0pzWkhkcFpHVWdSR1YyWld4dmNHVnlJRkpsYkdGMGFXOXVjeUJEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURUxNQWtHQTFVRUN3d0NSell4RXpBUkJnTlZCQW9NQ2tGd2NHeGxJRWx1WXk0eEN6QUpCZ05WQkFZVEFsVlRNQjRYRFRJek1Ea3hNakU1TlRFMU0xb1hEVEkxTVRBeE1URTVOVEUxTWxvd2daSXhRREErQmdOVkJBTU1OMUJ5YjJRZ1JVTkRJRTFoWXlCQmNIQWdVM1J2Y21VZ1lXNWtJR2xVZFc1bGN5QlRkRzl5WlNCU1pXTmxhWEIwSUZOcFoyNXBibWN4TERBcUJnTlZCQXNNSTBGd2NHeGxJRmR2Y214a2QybGtaU0JFWlhabGJHOXdaWElnVW1Wc1lYUnBiMjV6TVJNd0VRWURWUVFLREFwQmNIQnNaU0JKYm1NdU1Rc3dDUVlEVlFRR0V3SlZVekJaTUJNR0J5cUdTTTQ5QWdFR0NDcUdTTTQ5QXdFSEEwSUFCRUZFWWUvSnFUcXlRdi9kdFhrYXVESENTY1YxMjlGWVJWLzB4aUIyNG5DUWt6UWYzYXNISk9OUjVyMFJBMGFMdko0MzJoeTFTWk1vdXZ5ZnBtMjZqWFNqZ2dJSU1JSUNCREFNQmdOVkhSTUJBZjhFQWpBQU1COEdBMVVkSXdRWU1CYUFGRDh2bENOUjAxREptaWc5N2JCODVjK2xrR0taTUhBR0NDc0dBUVVGQndFQkJHUXdZakF0QmdnckJnRUZCUWN3QW9ZaGFIUjBjRG92TDJObGNuUnpMbUZ3Y0d4bExtTnZiUzkzZDJSeVp6WXVaR1Z5TURFR0NDc0dBUVVGQnpBQmhpVm9kSFJ3T2k4dmIyTnpjQzVoY0hCc1pTNWpiMjB2YjJOemNEQXpMWGQzWkhKbk5qQXlNSUlCSGdZRFZSMGdCSUlCRlRDQ0FSRXdnZ0VOQmdvcWhraUc5Mk5rQlFZQk1JSCtNSUhEQmdnckJnRUZCUWNDQWpDQnRneUJzMUpsYkdsaGJtTmxJRzl1SUhSb2FYTWdZMlZ5ZEdsbWFXTmhkR1VnWW5rZ1lXNTVJSEJoY25SNUlHRnpjM1Z0WlhNZ1lXTmpaWEIwWVc1alpTQnZaaUIwYUdVZ2RHaGxiaUJoY0hCc2FXTmhZbXhsSUhOMFlXNWtZWEprSUhSbGNtMXpJR0Z1WkNCamIyNWthWFJwYjI1eklHOW1JSFZ6WlN3Z1kyVnlkR2xtYVdOaGRHVWdjRzlzYVdONUlHRnVaQ0JqWlhKMGFXWnBZMkYwYVc5dUlIQnlZV04wYVdObElITjBZWFJsYldWdWRITXVNRFlHQ0NzR0FRVUZCd0lCRmlwb2RIUndPaTh2ZDNkM0xtRndjR3hsTG1OdmJTOWpaWEowYVdacFkyRjBaV0YxZEdodmNtbDBlUzh3SFFZRFZSME9CQllFRkFNczhQanM2VmhXR1FsekUyWk9FK0dYNE9vL01BNEdBMVVkRHdFQi93UUVBd0lIZ0RBUUJnb3Foa2lHOTJOa0Jnc0JCQUlGQURBS0JnZ3Foa2pPUFFRREF3Tm9BREJsQWpFQTh5Uk5kc2twNTA2REZkUExnaExMSndBdjVKOGhCR0xhSThERXhkY1BYK2FCS2pqTzhlVW85S3BmcGNOWVVZNVlBakFQWG1NWEVaTCtRMDJhZHJtbXNoTnh6M05uS20rb3VRd1U3dkJUbjBMdmxNN3ZwczJZc2xWVGFtUllMNGFTczVrPSIsIk1JSURGakNDQXB5Z0F3SUJBZ0lVSXNHaFJ3cDBjMm52VTRZU3ljYWZQVGp6Yk5jd0NnWUlLb1pJemowRUF3TXdaekViTUJrR0ExVUVBd3dTUVhCd2JHVWdVbTl2ZENCRFFTQXRJRWN6TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd0hoY05NakV3TXpFM01qQXpOekV3V2hjTk16WXdNekU1TURBd01EQXdXakIxTVVRd1FnWURWUVFERER0QmNIQnNaU0JYYjNKc1pIZHBaR1VnUkdWMlpXeHZjR1Z5SUZKbGJHRjBhVzl1Y3lCRFpYSjBhV1pwWTJGMGFXOXVJRUYxZEdodmNtbDBlVEVMTUFrR0ExVUVDd3dDUnpZeEV6QVJCZ05WQkFvTUNrRndjR3hsSUVsdVl5NHhDekFKQmdOVkJBWVRBbFZUTUhZd0VBWUhLb1pJemowQ0FRWUZLNEVFQUNJRFlnQUVic1FLQzk0UHJsV21aWG5YZ3R4emRWSkw4VDBTR1luZ0RSR3BuZ24zTjZQVDhKTUViN0ZEaTRiQm1QaENuWjMvc3E2UEYvY0djS1hXc0w1dk90ZVJoeUo0NXgzQVNQN2NPQithYW85MGZjcHhTdi9FWkZibmlBYk5nWkdoSWhwSW80SDZNSUgzTUJJR0ExVWRFd0VCL3dRSU1BWUJBZjhDQVFBd0h3WURWUjBqQkJnd0ZvQVV1N0Rlb1ZnemlKcWtpcG5ldnIzcnI5ckxKS3N3UmdZSUt3WUJCUVVIQVFFRU9qQTRNRFlHQ0NzR0FRVUZCekFCaGlwb2RIUndPaTh2YjJOemNDNWhjSEJzWlM1amIyMHZiMk56Y0RBekxXRndjR3hsY205dmRHTmhaek13TndZRFZSMGZCREF3TGpBc29DcWdLSVltYUhSMGNEb3ZMMk55YkM1aGNIQnNaUzVqYjIwdllYQndiR1Z5YjI5MFkyRm5NeTVqY213d0hRWURWUjBPQkJZRUZEOHZsQ05SMDFESm1pZzk3YkI4NWMrbGtHS1pNQTRHQTFVZER3RUIvd1FFQXdJQkJqQVFCZ29xaGtpRzkyTmtCZ0lCQkFJRkFEQUtCZ2dxaGtqT1BRUURBd05vQURCbEFqQkFYaFNxNUl5S29nTUNQdHc0OTBCYUI2NzdDYUVHSlh1ZlFCL0VxWkdkNkNTamlDdE9udU1UYlhWWG14eGN4ZmtDTVFEVFNQeGFyWlh2TnJreFUzVGtVTUkzM3l6dkZWVlJUNHd4V0pDOTk0T3NkY1o0K1JHTnNZRHlSNWdtZHIwbkRHZz0iLCJNSUlDUXpDQ0FjbWdBd0lCQWdJSUxjWDhpTkxGUzVVd0NnWUlLb1pJemowRUF3TXdaekViTUJrR0ExVUVBd3dTUVhCd2JHVWdVbTl2ZENCRFFTQXRJRWN6TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd0hoY05NVFF3TkRNd01UZ3hPVEEyV2hjTk16a3dORE13TVRneE9UQTJXakJuTVJzd0dRWURWUVFEREJKQmNIQnNaU0JTYjI5MElFTkJJQzBnUnpNeEpqQWtCZ05WQkFzTUhVRndjR3hsSUVObGNuUnBabWxqWVhScGIyNGdRWFYwYUc5eWFYUjVNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVFzd0NRWURWUVFHRXdKVlV6QjJNQkFHQnlxR1NNNDlBZ0VHQlN1QkJBQWlBMklBQkpqcEx6MUFjcVR0a3lKeWdSTWMzUkNWOGNXalRuSGNGQmJaRHVXbUJTcDNaSHRmVGpqVHV4eEV0WC8xSDdZeVlsM0o2WVJiVHpCUEVWb0EvVmhZREtYMUR5eE5CMGNUZGRxWGw1ZHZNVnp0SzUxN0lEdll1VlRaWHBta09sRUtNYU5DTUVBd0hRWURWUjBPQkJZRUZMdXczcUZZTTRpYXBJcVozcjY5NjYvYXl5U3JNQThHQTFVZEV3RUIvd1FGTUFNQkFmOHdEZ1lEVlIwUEFRSC9CQVFEQWdFR01Bb0dDQ3FHU000OUJBTURBMmdBTUdVQ01RQ0Q2Y0hFRmw0YVhUUVkyZTN2OUd3T0FFWkx1Tit5UmhIRkQvM21lb3locG12T3dnUFVuUFdUeG5TNGF0K3FJeFVDTUcxbWloREsxQTNVVDgyTlF6NjBpbU9sTTI3amJkb1h0MlFmeUZNbStZaGlkRGtMRjF2TFVhZ002QmdENTZLeUtBPT0iXX0.eyJub3RpZmljYXRpb25UeXBlIjoiVEVTVCIsIm5vdGlmaWNhdGlvblVVSUQiOiIyZTJhNmNlMC1lYzlmLTQwMzctYjFiZi02MThhZjg1NTI3YmQiLCJkYXRhIjp7ImFwcEFwcGxlSWQiOjY0NTAyODQ4NzIsImJ1bmRsZUlkIjoiY29tLm5pbmV0b24udGFsbGVyIiwiZW52aXJvbm1lbnQiOiJQcm9kdWN0aW9uIn0sInZlcnNpb24iOiIyLjAiLCJzaWduZWREYXRlIjoxNzI5MTM2NjUxMTI4fQ.9Lx1xURZbPDookrFeNCopt1gTHTT6jyZMOyfmxpiZeK9bhucvO7k6DWzwBSrVYkeNvgchHdJPFvX8be0A45aog';
    $result = Payment::config($config)->huawei(APayment::ENV_SANDBOX)->getJwtService()->decodedSignedData($jws);
    var_dump($result);
} catch (VerificationException|\Exception $e) {
    echo $e->getMessage();
}

/****************************************************
 * 订单状态查询（消耗型/非消耗型商品）
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-query-order-status-V5
 ****************************************************/
try {
    $purchaseOrderId = '210001867817339';
    $purchaseToken = '210001867817339';
    $appleIAPService = Payment::config($config)->huawei();
    $response = $appleIAPService->orderStatus($purchaseOrderId, $purchaseToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        //echo $response->getCode();
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
 * 订单确认发货（消耗型/非消耗型商品）
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-confirm-purchase-for-order-V5
 ****************************************************/
try {
    $purchaseOrderId = '210001867817339';
    $purchaseToken = '210001867817339';
    $appleIAPService = Payment::config($config)->huawei();
    $response = $appleIAPService->confirmOrder($purchaseOrderId, $purchaseToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        //echo $response->getCode();
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
 * 订阅状态查询
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-query-subscription-status-V5
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-query-subscription-status-V5#section149821246171719
 ****************************************************/
try {
    $purchaseOrderId = '210001867817339';
    $purchaseToken = '210001867817339';
    $appleIAPService = Payment::config($config)->huawei();
    $response = $appleIAPService->subscriptionStatus($purchaseOrderId, $purchaseToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        //echo $response->getCode();
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
 * 订阅确认发货
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-confirm-purchase-for-sub-V5
 ****************************************************/
try {
    $purchaseOrderId = '210001867817339';
    $purchaseToken = '210001867817339';
    $appleIAPService = Payment::config($config)->huawei();
    $response = $appleIAPService->confirmSubscription($purchaseOrderId, $purchaseToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        //echo $response->getCode();
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