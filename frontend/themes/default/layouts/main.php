<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\School;
use frontend\models\LoginForm;
use frontend\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$this->beginContent('@app/views/layouts/base.php');
?>
<!-- School modal starts -->
<div class="modal fade" id="modal-school" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4>切换学校</h4>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled">
                    <?php foreach (School::getKeyValuePairs() as $key => $name) :?>
                    <li><?= Html::a($name, ['/school/index', 'id' => $key], ['class' => 'btn btn-default btn-fill']) ?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--/ School modal ends -->
<?php if (Yii::$app->user->isGuest) :?>
<!-- Login modal starts -->
<div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4>用户登录</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-login',
                    'action' => ['/site/login'],
                    'enableAjaxValidation' => true,
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-3',
                            'wrapper' => 'col-sm-6',
                        ]
                    ]
                ]); ?>
                    <?php $loginForm = new LoginForm() ?>
                    <?= $form->field($loginForm, 'mobile') ?>
                    <?= $form->field($loginForm, 'password')->passwordInput() ?>
                    <?= $form->field($loginForm, 'rememberMe')->checkbox() ?>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-fill']) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <?= Html::a('忘记密码？', ['/site/request-password-reset']) ?>
                            <?= Html::a('没有帐号？去注册<i class="fa fa-long-arrow-right"></i>', ['/site/signup'], ['class' => 'pull-right']) ?>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<!--/ Login modal ends -->
<?php endif;?>

<!-- main wrapper -->
<div class="wrapper">

    <!-- header -->
    <header>
        <!-- header top section -->
        <div class="header-top">
            <div class="container">
                <ul class="list-unstyled">
                    <li>
                        <a href="" data-toggle="modal" data-target="#modal-school"><i class="fa fa-university"></i> <?= Yii::$app->params['schoolModel'] ? '<span class="hidden-xs">' . Yii::$app->params['schoolModel']->name . '</span><span class="visible-xs-inline">切换学校</span>' : '请选择学校' ?> <i class="fa fa-angle-down"></i></a>
                    </li>
                </ul>
                <ul class="list-unstyled pull-right">
                    <?php if (Yii::$app->user->isGuest) :?>
                    <li><a href="" data-toggle="modal" data-target="#modal-login"><i class="fa fa-user"></i> 登录</a></li>
                    <?php else :?>
                    <li>
                        <a id="link-user" class="dropdown-toggle" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false"><i class="fa fa-user"></i> 我的笑e购 <i class="fa fa-angle-down"></i></a>
                        <ul class="dropdown-menu list-unstyled" role="menu" aria-labelledby="link-user">
                            <li role="presentation"><?= Html::a('我的订单', ['/order']) ?></li>
                            <li role="presentation"><?= Html::a('账户设置', ['/i/profile']) ?></li>
                            <li role="presentation">
                                <?= Html::beginForm(['/site/logout']) ?>
                                <?= Html::submitButton('退出') ?>
                                <?= Html::endForm() ?>
                            </li>
                        </ul>
                    </li>
                    <?php endif;?>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- navigation menu with logo -->
        <nav class="navbar">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>"><?= Html::img($this->theme->getUrl('images/logo/logo.png'), ['class' => 'img-responsive']) ?></a>
                </div>
            </div><!-- /.container-fluid -->
        </nav>
    </header>
    <!-- header end -->
    
    <?= $content ?>
    
    <!-- footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <!-- footer widget -->
                    <div class="widget">
                        <!-- heading -->
                        <h5>笑e购（xiaoego.com）</h5>
                        <hr />
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <ul class="list-unstyled">
                                    <li><?= Html::a('关于我们', ['/site/about']) ?></li>
                                    <li><?= Html::a('帮助中心', ['/site/help']) ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <ul class="list-unstyled">
                                    <li><?= Html::a('加入我们', ['/site/joinus']) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 hidden-xs">
                    <!-- footer widget -->
                    <div class="widget">
                        <!-- heading -->
                        <h5>关于我们</h5>
                        <hr />
                        <!-- paragraph -->
                        <p>笑e购（xiaoego.com），最好用的校园即时购物平台。笑e购为您提供进口特产、休闲零食、饮料冲调和生活用品等数百种常用商品。在这里您可以享受到最贴心的价格、最优质的服务和最快速的物流，专注于大学生购物与配送服务。</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 hidden-xs">
                    <!-- footer widget -->
                    <div class="widget">
                        <!-- heading -->
                        <h5>联系我们</h5>
                        <hr />
                        <i class="fa fa-home fa-fw"></i> &nbsp; 无锡市惠山区文良路108号
                        <hr />
                        <i class="fa fa-phone-square fa-fw"></i> &nbsp; <?= Yii::$app->params['supportTel'] ?>
                        <hr />
                        <i class="fa fa-envelope fa-fw"></i> &nbsp; <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>"><?= Yii::$app->params['supportEmail'] ?></a>
                        <hr />
                        <i class="fa fa-wechat fa-fw"></i> &nbsp; xiaoegoguanfang
                    </div>
                </div>
            </div>
            <hr>
            <!-- Copyright info -->
            <p class="copy">Copyright &copy; <?= date('Y') ?> 杠杆科技  All rights are reserved. <?= Yii::$app->name ?> - <?= Yii::powered() ?></p>
        </div>
    </footer>
    <!-- footer end -->

</div>
<?php $this->endContent(); ?>