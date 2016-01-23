<?php
use yii\widgets\ListView;

$this->title = '我的订单';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order">
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'summaryOptions' => ['tag' => 'p', 'class' => 'text-right text-info'],
    'emptyTextOptions' => ['class' => 'callout callout-warning'],
    'emptyText' => '您还没有订单。',
    'itemView' => '_item'
]) ?>
</div>