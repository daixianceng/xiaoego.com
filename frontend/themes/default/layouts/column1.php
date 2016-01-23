<?php
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@app/views/layouts/main.php');
?>
<!-- school -->
<div class="school" style="background-image: url(<?= $this->theme->getUrl('images/banner/1.jpg') ?>);">
    <div class="container">
        <!-- school name -->
	    <h2><?= Yii::$app->params['schoolModel']->name ?></h2>
    </div>
</div>
<!-- school end -->

<!-- Goods modal starts -->
<div class="modal fade" id="modal-goods" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4></h4>
                <p></p>
                <div class="row">
                    <div class="col-xs-6 text-right">
                        <span class="price">单价：<i class="fa fa-cny"></i> <span>0.00</span></span>
                    </div>
                    <div class="col-xs-6">
                        <div class="quantity">
                            <button class="quantity-minus"><i class="fa fa-minus"></i></button>
                            <span class="quantity-count">0</span>
                            <button class="quantity-plus"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Goods modal ends -->

<!-- main content -->
<div class="main-content">
    <div class="container">
        <div class="inner-content">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>