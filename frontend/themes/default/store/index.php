<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Store;
use common\models\Category;
use xj\lazyload\LazyloadAsset;

/* @var $this yii\web\View */
/* @var $model common\models\Store */

LazyloadAsset::register($this);

$ajaxLoad = false;

$this->title = $model->name;
?>
<div class="row">
    <div class="col-lg-12">
        <?= $this->render('_promotion') ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-lg-3">
        <!-- storeinfo block -->
        <div class="storeinfo">
            <h2 class="br-red"><?= Html::a($model->name, ['/store/index', 'id' => $model->id]) ?><?= $model->status === Store::STATUS_REST ? '（休息中）' : '' ?><a class="storeinfo-edit" href="<?= Url::to(['/school/' . Yii::$app->params['schoolModel']->id]) ?>" title="切换营业点">[切换]</a></h2>
            <div class="storeinfo-content">
                <ul class="nav nav-pills nav-orange nav-justified">
                    <li role="presentation" class="active"><a href="#tab-content-0" data-toggle="tab" aria-expanded="true">商品分类</a></li>
                    <li role="presentation"><a href="#tab-content-1" data-toggle="tab" aria-expanded="false">店铺详情</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-content-0" class="tab-pane active">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <?php foreach (Category::getCategoryPairs() as $slug => $name) :?>
                                    <td><a href="<?= Url::to(['/store/index', 'id' => $model->id, 'category' => $slug]) ?>" class="link-cate" data-target="#cate-<?= $slug ?>"><?= Html::img($this->theme->getUrl('images/category/' . $slug . '.png')) ?><?= $name ?></a></td>
                                    <?php endforeach;?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="tab-content-1" class="tab-pane">
                        <ul class="storeinfo-list list-unstyled">
                            <li><i class="fa fa-tree fa-fw"></i> <?= Html::encode($model->address) ?></li>
                            <li><i class="fa fa-mobile fa-fw"></i> <?= Html::encode($model->cellphone) ?></li>
                            <?php if ($model->telephone) :?>
                            <li><i class="fa fa-phone fa-fw"></i> <?= Html::encode($model->telephone) ?></li>
                            <?php endif;?>
                            <?php if ($model->hours) :?>
                            <li><i class="fa fa-clock-o fa-fw"></i> <?= Html::encode($model->hours) ?></li>
                            <?php endif;?>
                            <?php if ($model->has_book || $model->has_down || $model->has_gift || !$model->has_least) :?>
                            <li>
                                <i class="fa fa-gift fa-fw"></i> 
                                <?php if ($model->has_down) :?>
                                <span class="feature feature-red" data-toggle="tooltip" data-placement="top" title="<?= $model->downMsg ?>">减</span>
                                <?php endif;?>
                                <?php if ($model->has_gift) :?>
                                <span class="feature feature-orange" data-toggle="tooltip" data-placement="top" title="<?= $model->giftMsg ?>">送</span>
                                <?php endif;?>
                                <?php if ($model->has_book) :?>
                                <span class="feature feature-blue-o" data-toggle="tooltip" data-placement="top" title="支持预定">预</span>
                                <?php endif;?>
                                <?php if (!$model->has_least) :?>
                                <span class="feature feature-purple-o" data-toggle="tooltip" data-placement="top" title="零元起送">零</span>
                                <?php endif;?>
                            </li>
                            <?php endif;?>
                            <?php if ($model->tags) :?>
                            <li>
                                <i class="fa fa-tags fa-fw"></i> 
                                <?php foreach ($model->tags as $tag) :?>
                                <span class="tag"><?= Html::encode($tag->name) ?></span>
                                <?php endforeach;?>
                            </li>
                            <?php endif;?>
                        </ul>
                    </div>
                </div>
                <?php if ($model->notice) :?>
                <!-- store notice block -->
                <div class="storeinfo-notice">
                    <div class="notice-content">
                        <span><i class="fa fa-bullhorn fa-fw"></i> <?= Html::encode($model->notice) ?></span>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
        <!-- shopping cart block -->
		<div class="shopping-cart shopping-cart-closed" data-storeId="<?= $model->id ?>" data-isRest="<?= $model->status === Store::STATUS_REST ? '1' : '0' ?>" data-least="<?= $model->has_least ? $model->least_val : '0.00' ?>">
		    <h2 class="br-red"><span>购物车</span><a class="shopping-clear" href="" title="清空购物车">[清空]</a></h2>
		    <div class="shopping-content">
		        <ul class="shopping-list list-unstyled">
		            <li class="shopping-empty"><i class="fa fa-refresh fa-spin"></i></li>
		        </ul>
		        <div class="shopping-bottom">
		            <div class="shopping-toggle visible-xs-block"><button><i class="fa fa-shopping-cart fa-3x"></i></button></div>
		            <div class="price"><i class="fa fa-cny"></i> <span>0.00</span></div>
		            <div class="shopping-btn">
		                <a href="<?= Url::to(['/order/create', 'id' => $model->id]) ?>" class="btn btn-danger disabled"><?= $model->status === Store::STATUS_REST ? '休息中' : '选好了 <i class="fa fa-long-arrow-right"></i>' ?></a>
		            </div>
		            <div class="clearfix"></div>
		        </div>
		    </div>
		</div>
    </div>
    <div class="col-md-8 col-lg-9">
        <!-- recent sale item block -->
        <div class="recent-sale">
            <?php if ($q !== '') : $ajaxLoad = true;?>
                <!-- heading -->
                <h2 class="br-orange default-head">搜索结果</h2>
                <div class="row">
                    <?php if ($goodsList = $model->getGoods()->andWhere(['or', ['like', 'name', $q], ['like', 'description', $q]])->limit(Yii::$app->params['goods.initialLimit'])->all()) :?>
                        <?php foreach ($goodsList as $goods) :?>
                        <?= $this->render('_item', ['goods' => $goods, 'lazy' => true]) ?>
                        <?php endforeach;?>
                        <div class="col-lg-12 loading"><div><i class="fa fa-refresh fa-spin"></i></div></div>
                    <?php else :?>
                        <div class="col-lg-12">
                            <div class="alert alert-warning">没有找到商品。</div>
                        </div>
                    <?php endif;?>
                </div>
            <?php elseif ($modelCate) : $ajaxLoad = true;?>
                <!-- heading -->
                <h2 class="br-orange default-head" id="cate-<?= $modelCate->slug ?>"><?= $modelCate->name ?></h2>
                <div class="row">
                    <?php if ($goodsList = $model->getGoods($modelCate->id)->limit(Yii::$app->params['goods.initialLimit'])->all()) :?>
                        <?php foreach ($goodsList as $goods) :?>
                        <?= $this->render('_item', ['goods' => $goods, 'lazy' => true]) ?>
                        <?php endforeach;?>
                        <div class="col-lg-12 loading"><div><i class="fa fa-refresh fa-spin"></i></div></div>
                    <?php else :?>
                        <div class="col-lg-12">
                            <div class="alert alert-warning">该分类暂无商品。</div>
                        </div>
                    <?php endif;?>
                </div>
            <?php else :?>
                <?php if ($model->layout === Store::LAYOUT_OPEN) :?>
                    <?php foreach (Category::find()->orderBy('sort ASC')->all() as $category) :?>
                    <?php if ($goodsList = $model->getGoods($category->id)->limit(Yii::$app->params['goods.displayLimit'])->all()) :?>
                    <!-- heading -->
                    <h2 class="br-orange default-head" id="cate-<?= $category->slug ?>"><?= $category->name ?><?= Html::a('[更多]', ['/store/index', 'id' => $model->id, 'category' => $category->slug], ['class' => 'more']) ?></h2>
                    <div class="row">
                        <?php foreach ($goodsList as $goods) :?>
                        <?= $this->render('_item', ['goods' => $goods, 'lazy' => true]) ?>
                        <?php endforeach;?>
                        <?php if (($count = $model->getGoods($category->id)->count() - Yii::$app->params['goods.displayLimit']) > 0) :?>
                        <?= $this->render('_more', ['url' => Url::to(['/store/index', 'id' => $model->id, 'category' => $category->slug]), 'count' => $count]) ?>
                        <?php endif;?>
                    </div>
                    <?php endif;?>
                    <?php endforeach;?>
                <?php else : $ajaxLoad = true;?>
                    <!-- heading -->
                    <h2 class="br-orange default-head">所有商品</h2>
                    <div class="row">
                        <?php if ($goodsList = $model->getGoods()->limit(Yii::$app->params['goods.initialLimit'])->all()) :?>
                        <?php foreach ($goodsList as $goods) :?>
                        <?= $this->render('_item', ['goods' => $goods, 'lazy' => true]) ?>
                        <?php endforeach;?>
                        <div class="col-lg-12 loading"><div><i class="fa fa-refresh fa-spin"></i></div></div>
                        <?php else :?>
                        <div class="col-lg-12">
                            <div class="alert alert-warning">该店铺暂无商品。</div>
                        </div>
                        <?php endif;?>
                    </div>
                <?php endif;?>
            <?php endif;?>
            
        </div>
        <!-- recent sale end -->
    </div>
</div>
<div class="fixed-tools">
    <div class="tool tool-pos1 totop hidden"><a href=""><i class="fa fa-arrow-up"></i></a></div>
    <div class="tool tool-pos0 tool-search"><a href=""><i class="fa fa-search"></i></a></div>
    <div class="tool-searchbox tool-pos0" style="display: none;">
        <?= Html::beginForm(Url::to(['/store/index', 'id' => $model->id]), 'get') ?>
        <div class="input-group">
            <?= Html::textInput('q', $q, ['class' => 'form-control', 'placeholder' => '商品搜索...']) ?>
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="fa fa-arrow-right"></i></button>
            </span>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
<?php
$js = <<<JS
if (Zhai.user.isGuest) {
    $('.shopping-empty').text('请您先登录~');
} else {
    Zhai.cart.refresh();
}
Zhai.quantity.refreshAll();

$("img.img-lazy").lazyload({effect:"fadeIn"});

if (($(window).height() + 100) < $(document).height()) {
    $('.totop').removeClass('hidden').affix({
        offset: {top:100}
    });
}
JS;

if ($ajaxLoad) {
    $offset = Yii::$app->params['goods.initialLimit'];
    $url = Url::to(['/store/load-goods', 'id' => $model->id, 'category' => $modelCate ? $modelCate->slug : 'all', 'q' => $q]);
    $js .= <<<JS
var pending = false;
var offset = {$offset};
var end = false;
$(window).scroll(function () {
    if (pending || end) return;
    if ($(document).height() - $(this).scrollTop() - $(this).height() < 300) {
        pending = true;
        $.ajax({
            url : '{$url}',
            type : 'post',
            data : {offset : offset},
            dataType : 'json',
            success : function (data) {
                if (data.status === 'ok') {
                    var \$html = $(data.html);
                    \$html.find('.link-view').click(Zhai.detail.viewHandle);
                    \$html.find('.sale-item').each(Zhai.goods.traversalHandle);
                    $('.recent-sale .loading').before(\$html);
                    offset += data.length;
                    end = data.end;
                    if (end) {
                        $('.recent-sale .loading').fadeOut();
                    }
                }
            },
            complete : function () {
                pending = false;
            },
            error : function () {}
        });
    }
});
JS;
}

$css = <<<CSS
@media (max-width: 767px){
    footer {
        padding-bottom: 60px !important;
    }
}
CSS;

$this->registerJs($js);
$this->registerCss($css);