<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Cosmo <daixianceng@gmail.com>
 */
class CsshakeAsset extends AssetBundle
{
    // The files are not web directory accessible, therefore we need
    // to specify the sourcePath property. Notice the @vendor alias used.
    public $sourcePath = '@vendor/csshake/css';
    public $css = [
        'csshake.min.css',
    ];
}