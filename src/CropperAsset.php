<?php

namespace guri\yii\cropper;

class CropperAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/cropperjs';

    public $js = ['cropper.js'];

    public $css = ['cropper.css'];

    public $publishOptions = [
        'only' => ['cropper.js', 'cropper.css'],
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
