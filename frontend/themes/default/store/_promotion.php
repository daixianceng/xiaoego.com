<?php
use common\helpers\Url;

/* @var $this yii\web\View */
?>
<div id="promotion" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000">
    <ol class="carousel-indicators">
        <li data-target="#promotion" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
        <div class="item active" style="background-image: url(<?= $this->theme->baseUrl ?>/images/promotion/2.jpg);">
            <a href="<?= Url::to(['/site/signup']) ?>">
                <h4 class="white text-center"><i class="fa fa-hand-o-right"></i> 新注册用户满30立减2元，立即注册<i class="fa fa-long-arrow-right"></i></h4>
            </a>
        </div>
    </div>
</div>