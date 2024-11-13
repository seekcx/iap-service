### 1. 通知类型(主通知+子通知)
### 1.1 主通知类型 ([服务端关键事件通知](https://developer.huawei.com/consumer/cn/doc/harmonyos-references-V5/iap-key-event-notifications-V5))
|事件|描述|
|---|---|
|DID_NEW_TRANSACTION|订单已购买/订阅已购买/订阅续订成功。|
|DID_CHANGE_RENEWAL_STATUS|订阅状态发生改变。|
|REVOKE|订单退款/撤销订阅。|
|RENEWAL_TIME_MODIFIED|订阅过期时间调整。|
|EXPIRE|订阅已过期。|
|SYNC|订单/订阅补发通知。在通知异常时，联系华为运营手动补发通知，此场景下无notificationSubtype。|

### 1.2 子通知类型
| 事件      |描述|
|---------|---|
|INITIAL_BUY|消耗型/非消耗型商品购买成功。 自动续期订阅商品的第一次购买成功。 使用主类型：DID_NEW_TRANSACTION|
|DID_RENEW|续期成功。 使用主类型：DID_NEW_TRANSACTION|
|RESTORE|用户主动恢复了一个订阅型商品，续期恢复正常。 使用主类型：DID_NEW_TRANSACTION|
|AUTO_RENEW_ENABLED|自动续期功能开启。 使用主类型：DID_CHANGE_RENEWAL_STATUS|
|AUTO_RENEW_DISABLED|自动续期功能关闭。 使用主类型：DID_CHANGE_RENEWAL_STATUS|
|DOWNGRADE|用户调整自动续期订阅商品降级或跨级且在下个续订生效。 使用主类型：DID_CHANGE_RENEWAL_STATUS或DID_NEW_TRANSACTION|
|UPGRADE|用户调整自动续期订阅商品升级或跨级且立即生效。 使用主类型：DID_NEW_TRANSACTION|
|REFUND_TRANSACTION|消耗型/非消耗型商品订单退款成功。 自动续期订阅商品订单退款成功。 使用主类型：REVOKE|
|BILLING_RETRY|一个到期的自动续期订阅商品进入账号保留期。 使用主类型：EXPIRE|
|PRICE_INCREASE|用户同意了涨价。 使用主类型：DID_CHANGE_RENEWAL_STATUS|
|BILLING_RECOVERY|订阅重试扣费成功。 使用主类型：DID_NEW_TRANSACTION|
|PRODUCT_NOT_FOR_SALE|商品不存在。 使用主类型：EXPIRE|
|APPLICATION_DELETE_SUBSCRIPTION_HOSTING|撤销订阅成功，订阅权益会立即取消。 使用主类型：REVOKE|