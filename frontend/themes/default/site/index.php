<?php
use yii\helpers\Html;
use common\models\School;
use frontend\assets\HomeAsset;

/* @var $this yii\web\View */

HomeAsset::register($this);
$this->title = '';
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (Yii::$app->user->isGuest) :?>
            <span class="top"><?= Html::a('登录', ['/site/login']) ?> | <?= Html::a('注册', ['/site/signup']) ?></span>
            <?php else :?>
            <span class="top"><?= Html::a('<i class="fa fa-user"></i> 我的笑e购', ['/i']) ?></span>
            <?php endif;?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= Html::img($this->theme->getUrl('images/logo/xiaoego.png'), ['class' => 'img-responsive img-zhai']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h4><i class="fa fa-map-marker"></i> 请选择所在学校</h4>
            <ul class="list-unstyled">
                <?php foreach (School::getKeyValuePairs() as $key => $name) :?>
                <li><?= Html::a($name, ['/school/index', 'id' => $key], ['class' => 'btn btn-outline-inverse btn-lg']) ?></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>