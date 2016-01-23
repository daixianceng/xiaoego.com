<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" data-isGuest="<?= Yii::$app->user->isGuest ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="keywords" content="网购,网上购物,在线购物,网购网站,网购商城,购物网站,网购中心,购物中心,笑e购,笑易购,校园O2O,xiaoego,xiaoego.com">
    <meta name="description" content="笑e购（xiaoego.com），最好用的校园O2O购物平台">
    <meta name="baseurl" content="<?= Url::base() ?>">
    <meta name="author" content="Cosmo">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <?= Html::csrfMetaTags() ?>
    <title><?= empty($this->title) ? '' : Html::encode($this->title) . ' - ' ?><?= Yii::$app->name ?></title>
    <?php $this->head() ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <?php $this->beginBody() ?>
    <?= $content; ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>