<?php
return [
    'adminEmail' => 'admin@xiaoego.com',
    'supportEmail' => 'support@xiaoego.com',
    'supportTel' => '0510-83295110',
    'supportPhone' => '18851510363',
    'user.passwordResetTokenExpire' => 3600,
    
    // 图片配置
    'imageBaseUrl' => 'http://image.xiaoego.com',
    
    // 商品配置
    'goods.coverPath' => '@image/cover',
    'goods.imagePath' => '@image/goods',
    'goods.defaultCoverUrl' => 'http://image.xiaoego.com/cover/default.png',
    'goods.defaultImageUrl' => 'http://image.xiaoego.com/goods/default.png',
    'goods.cartLimit' => 30,
    
    // 新用户满减优惠
    'enableNewDown' => true,
    'newDownUpper' => '30',
    'newDownVal' => '2.00',
    'newDownMsg' => '新用户满30立减2.00',
    
    // ping++配置
    'pingpp.apiKey' => 'sk_live_************************',
    'pingpp.appId' => 'app_****************',
    'pingpp.publicKeyPath' => '@common/data/rsa_public_key.pem'
];
