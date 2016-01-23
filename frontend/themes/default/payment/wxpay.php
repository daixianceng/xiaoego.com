<?php
use yii\helpers\Url;
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信安全支付</title>
    <script type="text/javascript">
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?= $jsApiParameters ?>,
            function(res){
                if (res.err_msg == 'get_brand_wcpay_request:ok') {
                    alert('微信支付成功！');
                    window.location.href = '<?= Url::to(['/order/detail', 'order' => $model->order_sn]) ?>';
                } else {
                    window.history.back();
                }
            }
        );
    }
    function callpay() {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
    callpay();
    </script>
</head>
<body>
</body>
</html>