<?php
use yii\helpers\Url;
use yii\helpers\Html;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

$route = Yii::$app->requestedAction->uniqueId;
$isCollapsed = Yii::$app->controller->id !== 'i';

$this->beginContent('@app/views/layouts/column2.php');
?>
<div class="row">
    <div class="col-md-12">
        <?= Alert::widget() ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-sm-4">
        <!-- inner navigation menu -->
        <div class="navi">
            <!-- heading -->
            <h3 class="br-red">个人中心</h3>
            <!-- list -->
            <ul class="list-unstyled">
                <li>
                    <a href="#collapse-accounts" data-toggle="collapse" aria-expanded="<?= $isCollapsed ? 'false' : 'true' ?>" class="<?= $isCollapsed ? 'collapsed' : '' ?>">
                        <i class="fa fa-user fa-fw"></i> 账户管理<i class="fa arrow"></i>
                    </a>
                    <ul id="collapse-accounts" class="list-unstyled subnav collapse<?= $isCollapsed ? '' : ' in' ?>" aria-expanded="<?= $isCollapsed ? 'false' : 'true' ?>">
                        <li<?= in_array($route, ['i/profile']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/i/profile']) ?>">基本信息</a></li>
                        <li<?= in_array($route, ['i/mobile']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/i/mobile']) ?>">手机</a></li>
                        <li<?= in_array($route, ['i/email']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/i/email']) ?>">邮箱</a></li>
                        <li<?= in_array($route, ['i/password']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/i/password']) ?>">密码</a></li>
                    </ul>
                </li>
                <li<?= in_array($route, ['order/index', 'order/detail']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/order']) ?>"><i class="fa fa-list-alt fa-fw"></i> 订单</a></li>
                <li<?= in_array($route, ['address/index', 'address/add', 'address/update']) ? ' class="active"' : '' ?>><a href="<?= Url::to(['/address']) ?>"><i class="fa fa-map-marker fa-fw"></i> 收货地址</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-9 col-sm-8">
        <!-- inner main content area -->
        <div class="inner-main">
            <!-- top heading -->
            <h2><?= Html::encode($this->title) ?></h2>
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>