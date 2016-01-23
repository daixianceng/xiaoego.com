<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Cosmo <daixianceng@gmail.com>
 */
class BaseAsset extends AssetBundle
{
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\cdn\AssetBundle'
    ];
}
