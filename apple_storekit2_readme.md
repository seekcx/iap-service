### 1. 通知类型(主通知+子通知)
### 1.1 主通知类型 ([服务端关键事件通知](https://developer.apple.com/documentation/appstoreservernotifications))
|事件|描述|
|---|---|
|SUBSCRIBED|如果subtype是INITIAL_BUY（首次购买），则用户首次通过“家人共享”购买或接收了对订阅的访问权限。如果是RESUBSCRIBE（重新购买/重新购买同一个组内的plan），则用户通过家庭共享重新订阅或接收了对同一订阅或同一订阅组内的另一个订阅的访问权限。|
|CONSUMPTION_REQUEST|一种通知类型，表示客户发起了应用内购买或自动续订消耗品的退款请求，且 App Store 要求您提供消费数据。有关详细信息，请参阅发送消费信息。|
|DID_RENEW|一种通知类型，与其子类型一起表示订阅已成功续订。如果子类型为 BILLING_RECOVERY，则表示之前未能续订的过期订阅已成功续订。如果子类型为空，则活动订购已成功自动续订到新的交易期。向客户提供订阅内容或服务的访问权限。|
|OFFER_REDEEMED|一种通知类型，与其 一起subtype指示用户兑换了促销优惠或优惠代码。如果subtype是INITIAL_BUY，则用户兑换了首次购买的优惠。如果是RESUBSCRIBE，则用户兑换了重新订阅非活动订阅的优惠。如果是UPGRADE，则用户兑换了升级其有效订阅的优惠，该优惠立即生效。如果是DOWNGRADE，则用户兑换了降级其有效订阅的优惠，该优惠将在下一个续订日期生效。|
|REFUND|一种通知类型，指示 AppStore已成功对消费品应用内购买、非消费品应用内购买、自动续订订阅或非续订订阅的交易进行退款。|
|DID_CHANGE_RENEWAL_STATUS|一种通知类型，与其一起subtype指示用户对订阅续订状态进行了更改。如果subtype=AUTO_RENEW_ENABLED，则用户重新启用订阅自动续订。如果是AUTO_RENEW_DISABLED，则用户禁用了订阅自动续费，或者用户申请退款后App Store禁用了订阅自动续费。|
|DID_CHANGE_RENEWAL_PREF|一种通知类型，与其一起subtype指示用户对其订阅计划进行了更改。如果subtype是UPGRADE，则用户升级了他们的订阅。升级立即生效，开始新的计费周期，用户将收到上一周期未使用部分的按比例退款。如果subtype是DOWNGRADE，则用户降级了他们的订阅。降级将在下一个续订日期生效，并且不会影响当前有效的计划。如果subtype为空，则用户将其续订首选项更改回当前订阅，从而有效地取消降级。|
|DID_FAIL_TO_RENEW|一种通知类型，与其一起subtype指示订阅由于计费问题而未能续订。订阅进入计费重试期。如果subtype是GRACE_PERIOD，则在宽限期内继续提供服务。如果为空，则说明订阅不在宽限期内，您可以停止提供订阅服务。|
|EXPIRED|如果subtype是VOLUNTARY，则订阅在用户禁用订阅续订后过期。如果subtype是BILLING_RETRY，则订阅已过期，因为计费重试期已结束，但没有成功的计费事务。如果是PRICE_INCREASE，则订阅已过期，因为用户不同意需要用户同意的价格上涨。如果是PRODUCT_NOT_FOR_SALE，则订阅已过期，因为在订阅尝试续订时该产品不可购买。|
|GRACE_PERIOD_EXPIRED|一种通知类型，指示计费宽限期已结束而无需续订订阅，因此您可以关闭对服务或内容的访问。通知用户他们的账单信息可能存在问题。|
|PRICE_INCREASE|一种通知类型，与其一起subtype表示系统已通知用户自动续订订阅价格上涨。如果涨价需要用户同意，是subtype指PENDING用户没有对涨价做出回应，或者ACCEPTED用户已经同意涨价。如果涨价不需要用户同意，那subtype就是ACCEPTED。|
|REFUND_DECLINED|一种通知类型，指示 AppStore 拒绝了应用开发者使用以下任一方法发起的退款请求|
|REFUND_REVERSED|一种通知类型，表明 App Store 由于客户提出的争议而撤销了之前授予的退款。如果您的应用因相关退款而撤销了内容或服务，则需要恢复它们。此通知类型可适用于任何应用内购买类型：消耗型、非消耗型、非续订订阅和自动续订订阅。对于自动续订订阅，当 App Store 撤销退款时，续订日期保持不变。|
|RENEWAL_EXTENDED|一种通知类型，指示 App Store 延长了特定订阅的订阅续订日期。您可以通过调用App Store Server API中的延长订阅续订日期或为所有活跃订阅者延长订阅续订日期来请求订阅续订日期延期。|
|RENEWAL_EXTENSION|一种通知类型，与其一起subtype表示 AppStore 正在尝试通过调用为所有活跃订阅者延长订阅续订日期 来延长您请求的订阅续订日期。如果subtype是SUMMARY，则 AppStore 已完成为所有符合条件的订阅者延长续订日期。|
|REVOKE|指示用户有权通过“家人共享”进行应用内购买的通知类型不再可通过共享进行。当购买者对其购买禁用“家庭共享”、购买者（或家庭成员）离开家庭群组或购买者收到退款时，AppStore 会发送此通知。您的应用程序也会收到呼叫。家庭共享适用于非消耗性应用内购买和自动续订订阅。有关家庭共享的更多信息，请参阅在应用程序中支持家庭共享。|
|TEST|当你调用请求测试通知端点时，App Store 服务器会发送的一种通知类型。调用该端点可测试你的服务器是否正在接收通知。只有在你提出请求时，你才会收到此通知。有关故障排除信息，请参阅获取测试通知状态端点。|
|EXTERNAL_PURCHASE_TOKEN|一种通知类型，连同其子类型 UNREPORTED 表明 Apple 为您的应用程序创建了外部购买令牌，但没有收到报告。有关报告令牌的更多信息，请参阅 externalPurchaseToken。此通知仅适用于使用外部购买提供替代支付选项的应用程序。|
|ONE_TIME_CHARGE|ONE_TIME_CHARGE 通知目前仅在沙盒环境中可用。表明客户购买了消耗品、非消耗品或不可续订的通知类型。当客户通过 “家庭共享 ”访问非消耗品时，App Store 也会发送此通知。有关自动续订购买的通知，请参阅 SUBSCRIBED 通知类型。|

### 1.2 子通知类型
| 事件      |描述|
|---------|---|
|ACCEPTED|适用于PRICE_INCREASE. 如果价格上涨需要客户同意，则带有此通知的通知表明客户同意订阅价格上涨；如果价格上涨不需要客户同意，则表明系统通知他们价格上涨。|
|AUTO_RENEW_DISABLED|适用于DID_CHANGE_RENEWAL_STATUS. 此类通知表明用户禁用了订阅自动续订，或者 App Store 在用户申请退款后禁用了订阅自动续订。|
|AUTO_RENEW_ENABLED|  适用于DID_CHANGE_RENEWAL_STATUS. 包含此信息的通知表明用户启用了订阅自动续订。|
|BILLING_RECOVERY | 适用于DID_RENEW. 出现此通知表示之前未能续订的过期订阅已成功续订。|
|BILLING_RETRY|  适用于EXPIRED. 此类通知表明订阅已过期，因为订阅在计费重试期结束之前未能续订。|
|DOWNGRADE|  适用于DID_CHANGE_RENEWAL_PREF. 包含此信息的通知表明用户降级了其订阅或交叉分级为具有不同持续时间的订阅。降级将在下一个续订日期生效。|
|FAILURE| 适用于RENEWAL_EXTENSION. 包含此信息的通知表明单个订阅的订阅续订日期延期失败。有关详细信息，请参阅中的对象。有关请求的信息，请参阅延长所有活跃订阅者的订阅续订日期。|
|GRACE_PERIOD| 适用于DID_FAIL_TO_RENEW. 包含此信息的通知表明订阅由于计费问题而无法续订。在宽限期内继续提供对订阅的访问。 |
|INITIAL_BUY |适用于SUBSCRIBED. 包含此内容的通知表示用户首次购买订阅或用户首次通过家人共享获得对订阅的访问权限。 |
|PENDING| 适用于PRICE_INCREASE. 出现此通知表示系统已通知用户订阅价格上涨，但用户尚未接受。 |
|PRICE_INCREASE| 适用于EXPIRED. 此类通知表明订阅已过期，因为用户不同意涨价。|
|PRODUCT_NOT_FOR_SALE| 适用于EXPIRED. 包含此内容的通知表明订阅已过期，因为在订阅尝试续订时无法购买该产品。 |
|RESUBSCRIBE| 适用于SUBSCRIBED. 带有此信息的通知表明用户通过家庭共享重新订阅或接收了对同一订阅或同一订阅组内的另一个订阅的访问权限。 |
|SUMMARY| 适用于RENEWAL_EXTENSION. 此通知表明 App Store 服务器已完成您为所有符合条件的订阅者延长订阅续订日期的请求。有关摘要详细信息，请参阅中的对象。有关请求的信息，请参阅延长所有活跃订阅者的订阅续订日期。 notificationTypesubtypesummaryresponseBodyV2DecodedPayload|
|UPGRADE| 适用于DID_CHANGE_RENEWAL_PREF. 包含此信息的通知表明用户已升级其订阅或交叉分级为具有相同持续时间的订阅。升级立即生效。|
|VOLUNTARY| 适用于EXPIRED. 此类通知表明订阅在用户禁用订阅自动续订后已过期。|
|UNREPORTED| 适用于 EXTERNAL_PURCHASE_TOKEN 通知类型。具有该子类型的通知表示 Apple 为您的应用程序创建了一个令牌，但没有收到报告。有关报告令牌的更多信息，请参阅 externalPurchaseToken。|

### 1.3 订阅服务的事件类型（包括首次订阅、重新订阅和成功自动续订）会收到以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| 客户首次订阅订阅组中的任何订阅。 | SUBSCRIBED | INITIAL_BUY |
| 客户重新订阅与其过期订阅属于同一订阅组的任何订阅。 | SUBSCRIBED | RESUBSCRIBE |
| 订阅成功自动续订。 | DID_RENEW |  |
| 购买者首次订阅后，家庭共享成员可以访问该订阅。 | SUBSCRIBED | INITIAL_BUY |
| 购买者重新订阅后，家庭共享成员可以访问该订阅。 | SUBSCRIBED | RESUBSCRIBE |

### 1.4 客户更改订阅选项（包括升级、降级或取消）会收到以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| 客户在同一订阅组中降级订阅。 | DID_CHANGE_RENEWAL_PREF | DOWNGRADE |
| 客户恢复到之前的订阅，实际上取消了降级。 | DID_CHANGE_RENEWAL_PREF |  |
| 客户在同一订阅组中升级订阅。 | DID_CHANGE_RENEWAL_PREF | UPGRADE |
| 客户在 App Store 订阅设置页面取消订阅。 | DID_CHANGE_RENEWAL_STATUS | AUTO_RENEW_DISABLED |
| 客户取消订阅后再次订阅，从而重新启用自动续订。 | DID_CHANGE_RENEWAL_STATUS | AUTO_RENEW_ENABLED |
| 由于客户通过您的应用程序使用退款请求 API 发起了退款，系统禁用了自动续订。 | DID_CHANGE_RENEWAL_STATUS | AUTO_RENEW_DISABLED |

### 1.5 客户在兑换订阅优惠（如促销优惠、优惠代码或回馈优惠）时，会收到以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| 客户为有效订阅兑换促销优惠或优惠码。 | OFFER_REDEEMED |  |
| 客户兑换优惠码以首次订阅。 | SUBSCRIBED | INITIAL_BUY |
| 客户在其订阅过期后兑换促销优惠、优惠码或忠诚度优惠。 | SUBSCRIBED | RESUBSCRIBE |
| 客户兑换促销优惠或优惠码以升级其订阅。 | OFFER_REDEEMED | UPGRADE |
| 客户兑换促销优惠并降级其订阅。 | OFFER_REDEEMED | DOWNGRADE |

### 1.6 计费事件（包括计费重试、进入和退出计费宽限期以及订阅到期）会导致以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| 由于客户选择取消订阅，订阅过期。 | EXPIRED | VOLUNTARY |
| 由于开发者将订阅从销售中移除且续订失败，订阅过期。 | EXPIRED | PRODUCT_NOT_FOR_SALE |
| 由于账单重试期结束且未能恢复订阅，订阅过期。 | EXPIRED | BILLING_RETRY |
| 订阅续订失败并进入账单重试期。 | DID_FAIL_TO_RENEW |  |
| 订阅续订失败并进入启用了账单宽限期的账单重试期。 | DID_FAIL_TO_RENEW | GRACE_PERIOD |
| 账单重试成功恢复订阅。 | DID_RENEW | BILLING_RECOVERY |
| 订阅退出账单宽限期（并继续在账单重试中）。 | GRACE_PERIOD_EXPIRED |  |

### 1.7 提高自动续订价格时发生的事件或通知，会导致以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| 系统通知客户关于自动续订订阅价格上涨需要客户同意，但客户没有回应。 | PRICE_INCREASE | PENDING |
| 自动续订订阅因客户未同意价格上涨而过期。 | EXPIRED | PRICE_INCREASE |
| 客户同意自动续订订阅的价格上涨。 | PRICE_INCREASE | ACCEPTED |
| 系统通知客户关于自动续订订阅的价格上涨，该上涨不需要客户同意。 | PRICE_INCREASE | ACCEPTED |
| 客户在收到价格上涨通知或请求同意价格上涨后取消订阅。 | DID_CHANGE_RENEWAL_STATUS |  |

### 1.8 开发人员申请延长订阅日期会收到以下通知：
| 事件 | 通知类型 | 通知子类型 |
|---|---|---|
| App Store 成功延长特定订阅的续订日期。 | RENEWAL_EXTENDED |  |
| App Store 成功完成所有符合条件的订阅者的续订日期延长。 | RENEWAL_EXTENSION | SUMMARY |
| App Store 未能延长特定订阅者的续订日期。 | RENEWAL_EXTENSION | FAILURE |