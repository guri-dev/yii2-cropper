<?php

namespace guri\yii\cropper;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/punjabideveloper/yii2-cropper/assets';

    public $js = ['script.js'];

    public $css = ['styles.css'];

    public $depends = [CropperAsset::class];
}
