<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Cosmo <daixianceng@gmail.com>
 */
class BaseAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\cdn\AssetBundle'
    ];
}
