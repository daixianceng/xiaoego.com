<?php
use yii\helpers\Html;
?>
<?php if ($cartGoodsList = Yii::$app->user->identity->getCartGoods($storeId)->all()) :?>
<?php foreach ($cartGoodsList as $cartGoods) :?>
    <?php if ($cartGoods->isExpired) :?>
        <li class="shopping-expired" data-goodsId="<?= $cartGoods->goods_id ?>" title="该商品已失效">
            <div class="name"><?= Html::encode($cartGoods->goods->name) ?></div>
            <div class="price">
                <i class="fa fa-cny"></i> 
                <?= $cartGoods->goods->price ?>
            </div>
            <div class="delete"><button type="button" title="从购物车中删除"><i class="fa fa-times"></i></button></div>
            <div class="clearfix"></div>
        </li>
    <?php else :?>
        <li class="shopping-item" data-goodsId="<?= $cartGoods->goods_id ?>">
            <div class="name"><a class="link-view" href="#" data-goodsId="<?= $cartGoods->goods_id ?>" title="<?= Html::encode($cartGoods->goods->name) ?>"><?= Html::encode($cartGoods->goods->name) ?></a></div>
            <div class="price">
                <i class="fa fa-cny"></i> 
                <?= $cartGoods->goods->price ?>
            </div>
            <div class="quantity" data-goodsId="<?= $cartGoods->goods_id ?>" data-max="<?= $cartGoods->goods->surplus ?>">
                <button class="quantity-minus"><i class="fa fa-minus"></i></button>
                <span class="quantity-count"><?= $cartGoods->count ?></span>
                <button class="quantity-plus"><i class="fa fa-plus"></i></button>
            </div>
            <div class="clearfix"></div>
        </li>
    <?php endif;?>
<?php endforeach;?>
<?php else :?>
<li class="shopping-empty">购物车是空的~</li>
<?php endif;?>