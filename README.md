## 介绍

---

IAP-Service 组件集成了苹果(apple)、华为(huawei)应用内支付服务(IAP)，统一封装 IAP 服务器 REST API，封装 IAP 服务端通知，方便开发者快速集成支付功能，专注业务开发。

## 环境依赖

---

- PHP >= 7.1.2
- ext-json
- ext-curl
- ext-openssl
- guzzlehttp/guzzle: ^6.5 || ^7.0
- phpseclib/phpseclib: ^3.0
- firebase/php-jwt: ^6.10

## 安装

---

1、通过命令行下安装：

```bash
composer require "simplephp/iap-service:1.0.*"
```

2、通过项目下composer.json文件配置方式安装：

```yaml
"require": {
    "simplephp/iap-service": "1.0.*"
}
```

## 食用

---

1、配置详解
```php
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
```
2、集成示例 (示例详情请查看examples目录)

```bash
├── examples
│   ├── ....
│   ├── app_store_notify.php      // apple iap 事件通知示例
│   ├── app_store_server_api.php  // apple iap rest api 示例
│   └── huawei_server_api.php     // huawei iap rest api 示例
│   └── huawei_server_notify.php  // huawei iap 事件通知示例
├── ...
```

## 说明

---
- [App Store IAP-事件通知](./apple_storekit2_readme.md)
- [Huawei IAP-事件通知](./huawei_iap_readme.md)
- 大自然的搬运工,相关接口文档请查看官方文档
  - [App Store-服务器通知](https://developer.apple.com/documentation/AppStoreServerNotifications)
  - [App Store-应用商店服务器 API](https://developer.apple.com/documentation/appstoreserverapi)
  - [华为-服务器通知](https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-key-event-notifications-V5)
  -  [华为-应用商店服务器 API](https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-rest-V5)
- 有问题请提issues, 有能力的朋友请提交PR, 谢谢！