<?php
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '下单成功';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-success create-success" role="alert">恭喜您下单成功！<span>5</span>秒后跳转至订单详情页。</div>
<?php
$redirect = Url::to(['/order/detail', 'order' => $order->order_sn]);
$js = <<<JS
var s = 5;
setInterval(function () {
    s -= 1;
    if (s === 0) {
        window.location.href = '{$redirect}';
    } else {
        $('.create-success span').text(s);
    }
}, 1000);
JS;
$this->registerJs($js);