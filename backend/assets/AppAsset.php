<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Cosmo <daixianceng@gmail.com>
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/site.js'
    ];
    public $depends = [
        'backend\assets\BaseAsset',
        'backend\assets\MetisMenuAsset'
    ];
}