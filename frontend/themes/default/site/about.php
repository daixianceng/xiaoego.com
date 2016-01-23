<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = '关于我们';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <div class="page-section">
        <?= Html::img($this->theme->getUrl('images/team.jpg'), ['class' => 'img-thumbnail img-left']) ?>
        <p>笑e购（xiaoego.com）是杠杆科技旗下的移动便利网站。专注于校园即时电商领域，定位于5分钟可送达的口袋便利店。宅男宅女必备，专治懒货宅客，及永远吃不饱的Food Junkie！总的来说，我们就是想让幸福来的再快一些。</p>
        <p>—— 一群在路上折腾的创业者 </p>
    </div>
</div>