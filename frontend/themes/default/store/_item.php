<?php
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $goods common\models\Goods */
/* @var $lazy boolean */

$quantity = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->getCartGoodsQuantity($goods->id);
?>
<div class="col-lg-3 col-sm-4">
    <!-- recent item for slider -->
    <div class="sale-item" data-goodsId="<?= $goods->id ?>">
        <!-- goods tag -->
        <?php if ($goods->is_promotion) :?>
        <span class="hot br-purple">促</span>
        <?php elseif ($goods->is_hot) :?>
        <span class="hot br-red">热</span>
        <?php elseif ($goods->is_new) :?>
        <span class="hot br-brown">新</span>
        <?php endif;?>
        <!-- item image -->
        <div class="sale-pic">
            <a class="link-view" href="#" data-goodsId="<?= $goods->id ?>">
                <?php if ($lazy) :?>
                <img class="img-responsive img-lazy" src="<?= Yii::$app->params['goods.defaultCoverUrl'] ?>" data-original="<?= Url::toCover($goods->cover) ?>" />
                <?php else :?>
                <img class="img-responsive" src="<?= Url::toCover($goods->cover) ?>" />
                <?php endif;?>
            </a>
        </div>
        <div class="sale-detail">
            <!-- item name / title -->
            <h3><?= $goods->name ?></h3>
            <div class="sale-row">
                <div class="sales">月售：<?= $goods->sales ?></div>
                <div class="stock">库存：<span><?= $goods->surplus ?></span></div>
                <div class="clearfix"></div>
            </div>
            <!-- button -->
            <div class="cart-btn">
                <div class="price">&yen;<span><?= $goods->price ?></span><?= $goods->price_original > 0 ? ' <del>' . $goods->price_original . '</del>' : '' ?></div>
                <div class="quantity" data-goodsId="<?= $goods->id ?>" data-max="<?= $goods->surplus ?>">
                    <button class="quantity-minus"><i class="fa fa-minus"></i></button>
                    <span class="quantity-count"><?= $quantity ?></span>
                    <button class="quantity-plus"><i class="fa fa-plus"></i></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>