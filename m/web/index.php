<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$isWechat = strpos($userAgent, 'MicroMessenger') !== false;
if ($isWechat) {
    if (session_status() != PHP_SESSION_ACTIVE) {
        @session_start();
    }
    if (!isset($_SESSION['wechatOpenid'])) {
        require(__DIR__ . '/../../vendor/pingplusplus/pingpp-php/init.php');

        $appId = '******************';
        $appSecret = '********************************';
        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        if (!isset($_GET['code'])){
            $url = \Pingpp\WxpubOAuth::createOauthUrlForCode($appId, $currentUrl);
            header('Location: ' . $url);
            exit();
        } else {
            $code = $_GET['code'];
            $_SESSION['wechatOpenid'] = \Pingpp\WxpubOAuth::getOpenid($appId, $appSecret, $code);
        }
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="author" content="Cosmo">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <title>笑e购</title>

    <link href="lib/ionic/css/ionic.min.css" rel="stylesheet">
    <link href="css/fonts.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- ionic/angularjs js -->
    <script src="lib/ionic/js/ionic.bundle.min.js"></script>
    
    <!-- Ping++ -->
    <script src="js/pingpp.min.js"></script>

    <!-- your app's js -->
    <script src="js/app.js"></script>
    <script src="js/controllers.js"></script>
    <script src="js/services.js"></script>
    <script src="js/config.js"></script>
    <?php if ($isWechat) : ?>
    <script src="js/ap.js"></script>
    <script type="text/javascript">
    window.isWechat = true;
    window.WC_OPEN_ID = '<?= $_SESSION['wechatOpenid'] ?>'
    </script>
    <?php endif; ?>
  </head>
  <body ng-app="starter">
    <ion-nav-bar class="bar-assertive nav-title-slide-ios7"></ion-nav-bar>
    <ion-nav-view></ion-nav-view>
  </body>
</html>
