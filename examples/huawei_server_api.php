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
    $jws = 'eyJ4NWMiOlsiTUlJQ3d6Q0NBa21nQXdJQkFnSU9DZnF2WmxNY2JOQU0ySlp6S1g0d0NnWUlLb1pJemowRUF3TXdaekVMTUFrR0ExVUVCZ3dDUTA0eER6QU5CZ05WQkFvTUJraDFZWGRsYVRFVE1CRUdBMVVFQ3d3S1NIVmhkMlZwSUVOQ1J6RXlNREFHQTFVRUF3d3BTSFZoZDJWcElFTkNSeUJCY0hCc2FXTmhkR2x2YmlCSmJuUmxjbWR5WVhScGIyNGdRMEVnUnpNd0hoY05NalF3TXpBeE1EWTBNVEEyV2hjTk1qWXdNekF4TURZME1UQTJXakJ2TVFzd0NRWURWUVFHRXdKRFRqRVBNQTBHQTFVRUNnd0dTSFZoZDJWcE1Ta3dKd1lEVlFRTERDQklkV0YzWldrZ1EwSkhJRU5zYjNWa0lGTmxZM1Z5YVhSNUlGTnBaMjVsY2pFa01DSUdBMVVFQXd3YlNIVmhkMlZwSUVOQ1J5QkpiaTFCY0hBZ1VIVnlZMmhoYzJWek1Ga3dFd1lIS29aSXpqMENBUVlJS29aSXpqMERBUWNEUWdBRStjak5YRGczK0RTbXpXUC8rbG5xYVNJOENjMEVTUFE5R25DYkR1cDR2SEdaZ3NiOHk0dm1YMWYyVEQrd2ZDVzBPWjRDcHFHMlpWaXpHK3Job3IrQTI2T0IwRENCelRBTUJnTlZIUk1CQWY4RUFqQUFNRmtHQTFVZEh3UlNNRkF3VHFCTW9FcUdTR2gwZEhBNkx5OW9OV2h2YzNScGJtY3RaSEpqYmk1a1ltRnVhMk5rYmk1amJpOWpZMmcxTDJOeWJDOW9ZV2xqWVdjekwwaDFZWGRsYVVOQ1IwaEJTVWN6WTNKc0xtTnliREFmQmdOVkhTTUVHREFXZ0JSdEpsSFd2TW8zaGZSODlIdzYzZnNJVHM3MjZEQVNCZ3dyQmdFRUFZOWJBb01mQVFFRUFnVUFNQjBHQTFVZERnUVdCQlRwVkJrM1JVRjVZRSs5Rm1lN3N0QU8xVkNuNGpBT0JnTlZIUThCQWY4RUJBTUNBK2d3Q2dZSUtvWkl6ajBFQXdNRGFBQXdaUUl3U1ZhbFhiQ2s2NW9UaTNNQzJjYlp1NkFZQk1yUHJIRDM2YU9Ra0dRSEZXL1NxcGEwV2wxOWY5a1MwNEo3Nnp2L0FqRUF2Z0RLK0VsWFI2alJqekl2K05zd2FsYi9HVDQvd1UxK0o5NzZpaXdnbEVoTWg1UHB1bnJiT2xTU09DaU1YT3RkIiwiTUlJQzVEQ0NBbXVnQXdJQkFnSUljSHhrbUoyNlp1TXdDZ1lJS29aSXpqMEVBd013VXpFTE1Ba0dBMVVFQmhNQ1EwNHhEekFOQmdOVkJBb01Ca2gxWVhkbGFURVRNQkVHQTFVRUN3d0tTSFZoZDJWcElFTkNSekVlTUJ3R0ExVUVBd3dWU0hWaGQyVnBJRU5DUnlCU2IyOTBJRU5CSUVjeU1CNFhEVEl5TURVeU5EQXhOVEl4TjFvWERUUXlNRFV5TkRBeE5USXhOMW93WnpFTE1Ba0dBMVVFQmd3Q1EwNHhEekFOQmdOVkJBb01Ca2gxWVhkbGFURVRNQkVHQTFVRUN3d0tTSFZoZDJWcElFTkNSekV5TURBR0ExVUVBd3dwU0hWaGQyVnBJRU5DUnlCQmNIQnNhV05oZEdsdmJpQkpiblJsY21keVlYUnBiMjRnUTBFZ1J6TXdkakFRQmdjcWhrak9QUUlCQmdVcmdRUUFJZ05pQUFTVitMTmplcHlaVm5rdmxvakRSWXFYZGdJRWh3WERtZW5QR2VhZUlXUkdSR29rL1B5ZFVIbWI3d1h3dTZsUUlRWHJVMGNwRk5JckQyN3NXVzR4SllSZi95RUVDbFc0Qjg3QXlVaUZoc2hRU0ZaM1BVdFc3Y2RKaUdmQ0tUSmROQ0NqZ2Zjd2dmUXdId1lEVlIwakJCZ3dGb0FVbzQ1YTlWcThjWXdxYWlWeWZraVM0cExjSUFBd0hRWURWUjBPQkJZRUZHMG1VZGE4eWplRjlIejBmRHJkK3doT3p2Ym9NRVlHQTFVZElBUS9NRDB3T3dZRVZSMGdBREF6TURFR0NDc0dBUVVGQndJQkZpVm9kSFJ3T2k4dmNHdHBMbU52Ym5OMWJXVnlMbWgxWVhkbGFTNWpiMjB2WTJFdlkzQnpNQklHQTFVZEV3RUIvd1FJTUFZQkFmOENBUUF3RGdZRFZSMFBBUUgvQkFRREFnRUdNRVlHQTFVZEh3US9NRDB3TzZBNW9EZUdOV2gwZEhBNkx5OXdhMmt1WTI5dWMzVnRaWEl1YUhWaGQyVnBMbU52YlM5allTOWpjbXd2Y205dmRGOW5NbDlqY213dVkzSnNNQW9HQ0NxR1NNNDlCQU1EQTJjQU1HUUNNSE9HbmNrWlkwNkR2aFl1QmNRQXB1K1ZmTkgvZFpSZzNOTzlZWm1hRkVuZG52dlRkblR4anBkenRnbjZrT0ZsaXdJd1BLZHZHeHVYdlJuV1VsWHRNTUY0cjFzRDlHQ1RsQ1hWZVJQY1RFSThtR0U1eVBNY3hBVmhMNHF1a1paQnp6SmIiLCJNSUlDR2pDQ0FhR2dBd0lCQWdJSVNoaHBuNTE5ak5Bd0NnWUlLb1pJemowRUF3TXdVekVMTUFrR0ExVUVCaE1DUTA0eER6QU5CZ05WQkFvTUJraDFZWGRsYVRFVE1CRUdBMVVFQ3d3S1NIVmhkMlZwSUVOQ1J6RWVNQndHQTFVRUF3d1ZTSFZoZDJWcElFTkNSeUJTYjI5MElFTkJJRWN5TUI0WERUSXdNRE14TmpBek1EUXpPVm9YRFRRNU1ETXhOakF6TURRek9Wb3dVekVMTUFrR0ExVUVCaE1DUTA0eER6QU5CZ05WQkFvTUJraDFZWGRsYVRFVE1CRUdBMVVFQ3d3S1NIVmhkMlZwSUVOQ1J6RWVNQndHQTFVRUF3d1ZTSFZoZDJWcElFTkNSeUJTYjI5MElFTkJJRWN5TUhZd0VBWUhLb1pJemowQ0FRWUZLNEVFQUNJRFlnQUVXaWRrR25EU093My9IRTJ5MkdIbCtmcFdCSWE1UytJbG5OcnNHVXZ3QzFJMlFXdnRxQ0hXbXdGbEZLOTV6S1hpTThzOXlWM1ZWWGg3aXZOOFpKTzNTQzVOMVRDcnZCMmxwSE1Cd2N6NERBMGtnSENNbS93RGVjNmtPSHgxeHZDUm8wSXdRREFPQmdOVkhROEJBZjhFQkFNQ0FRWXdEd1lEVlIwVEFRSC9CQVV3QXdFQi96QWRCZ05WSFE0RUZnUVVvNDVhOVZxOGNZd3FhaVZ5ZmtpUzRwTGNJQUF3Q2dZSUtvWkl6ajBFQXdNRFp3QXdaQUl3TXlwZUI3UDBJYlk3YzZncFdjQ2xoUnpuT0pGajh1YXZyTnUyUElvejlLSXFyM2puQmxCSEpzMG15STdudFlwRUFqQmJtOGVETVpZNXpxNWlNWlVDNkg3VXpZU2l4NFV5MVlsc0xWVjczOFB0S1A5aEZUamdESGN0WEpsQzVMNytaRFk9Il0sImFsZyI6IkVTMjU2IiwidHlwIjoiSldUIn0.eyJub3RpZmljYXRpb25UeXBlIjoiRVhQSVJFIiwibm90aWZpY2F0aW9uU3VidHlwZSI6IkJJTExJTkdfUkVUUlkiLCJub3RpZmljYXRpb25SZXF1ZXN0SWQiOiI3OTI0MWEzMzU2MTY1ODgyNTc3ZWI1NWI1YzY2YjFlOThiYjEzZThmZDg3YWViYjBiOGY1NzZjYjc2ZTM5MWRjIiwibm90aWZpY2F0aW9uTWV0YURhdGEiOnsiZW52aXJvbm1lbnQiOiJTQU5EQk9YIiwiYXBwbGljYXRpb25JZCI6IjU3NjU4ODAyMDc4NTQ2NjQ0NjciLCJwYWNrYWdlTmFtZSI6ImNvbS5mYnguaG0uaGFuZHdyaXRlaW1lIiwidHlwZSI6MiwiY3VycmVudFByb2R1Y3RJZCI6Im9uZV9tb250aF9hcl9tZW1iZXJzaGlwIiwic3ViR3JvdXBJZCI6IkZCOEE0MjE4RDY3MjQ0NjRBNzA0MTExNkE3ODYxOEYxIiwic3ViR3JvdXBHZW5lcmF0aW9uSWQiOiI3MjE4ODE2MTAyNDZkMGU2NDc0MTRhZWU4MTNkZDAwOTMyNzc0MmIzZWRhN2E3ZjNlNjk0YTMyMWJmM2Y3ZDNlIiwic3Vic2NyaXB0aW9uSWQiOiIxNzMzNDcyNTAxMDMyLkQ5MzhBMkIxLjI1NTQiLCJwdXJjaGFzZVRva2VuIjoiMDAwMDAxOTM5YjAzM2QyZjQ5NDIzNDA1OTIwMzMzODc2YWUwMmM0YzU2OTZhNzFlZDAyY2YwMzBiZGExYzJmMTRhNzdlOTI4ZTU3ZDYxODN4NDM0ZS4xLjI1NTQiLCJwdXJjaGFzZU9yZGVySWQiOiIxNzMzNDczMzkxNzkyLjQxMTIwNjJFLjI1NTQifSwibm90aWZpY2F0aW9uVmVyc2lvbiI6InYzIiwic2lnbmVkVGltZSI6MTczMzQ3NDMyMTQ1M30.QGSH7fFpRsTM0LF4I-p9TftQGheXqsqcaBB6eXgFEGe-c_yeZpYgQkhSXL39UvyZ0F_G-jhD01mPEi6s2d3WWw';
    $result = Payment::config($config)->huawei(APayment::ENV_SANDBOX)->getJwtService()->decodedSignedData($jws);
   // var_dump($result);
} catch (VerificationException|\Exception $e) {
    //echo $e->getMessage();
}

/****************************************************
 * 订单状态查询（消耗型/非消耗型商品）
 * @link https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-query-order-status-V5
 ****************************************************/
try {
    $purchaseOrderId = '1733192171980.8001BC3A.2554';
    $purchaseToken = '000001938a489d40d494e12855ced06ca69cd65a1ca1f1cafa931ed30c0885e995d74fd8cca88bedx434e.1.2554';
    $appleIAPService = Payment::config($config)->huawei();
    $response = $appleIAPService->subscriptionStatus($purchaseOrderId, $purchaseToken);
    if ($response->isSuccess()) {
        $result = $response->getData();
        var_dump($result);
    } else {
        echo $response->getCode();
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