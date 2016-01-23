<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收货地址';
$this->params['breadcrumbs'][] = ['label' => '个人中心', 'url' => ['/i']];
$this->params['breadcrumbs'][] = $this->title;
?>
<p class="list-header">
    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> 添加', ['/address/add'], ['class' => 'btn btn-primary']) ?>
</p>
<div class="myaddress">
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'summaryOptions' => ['tag' => 'p', 'class' => 'text-right text-info'],
    'emptyTextOptions' => ['class' => 'callout callout-warning'],
    'emptyText' => '您还没有收货地址。',
    'itemView' => '_item'
]) ?>
</div>