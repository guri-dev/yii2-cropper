<?php

namespace guri\yii\cropper;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;


class Widget extends \yii\widgets\InputWidget
{
    public $format;

    public $containerOptions = [];

    public $clientOptions = [];

    public $onClientUpdate;

    public $uploadButtonOptions = [];

    public $rotateButtonOptions = [];

    public $rotateCcwLabel;

    public $rotateCwLabel;

    public $layout = "{canvas}\n{upload}\n{rotate-cw}\n{rotate-ccw}\n{image}";

    public $size = 'viewport';

    public $url;

    public function init()
    {
        parent::init();

        Html::addCssClass($this->containerOptions, ['croppie-widget']);
        Html::addCssClass($this->options, ['croppie-widget__data']);

        Html::addCssClass(
            $this->uploadButtonOptions,
            ['croppie-widget__upload btn btn-default']
        );

        Html::addCssClass(
            $this->rotateButtonOptions,
            ['croppie-widget__rotate btn btn-default']
        );

        if ($this->format === NULL) {
            $this->format = 'png';
        }
    }


    protected function renderCanvas()
    {
        $id = $this->id . '__canvas';

        $js_options = $this->clientOptions
            ? Json::encode($this->clientOptions)
            : '';

        $format = Json::encode($this->format);
        $size = Json::encode($this->size);

        if ($this->onClientUpdate) {
            $callback = 'function($widget, $input) {'
                . $this->onClientUpdate
                . ';}';
        } else {
            $callback = 'null';
        }

        return Html::tag('div', '', [
            'id' => $id,
            'class' => ['croppie-widget__canvas'],
        ]);
    }


    protected function renderButton()
    {
        return Html::tag('input', '', [
            'id' => 'imageupload-image',
            'type' => 'file',
            'class' => ['form-control-file'],
            'name' => ['ImageUpload[image]'],
        ]);
    }


    protected function renderRotateButton($label, $degrees)
    {
        return Html::button(
            $label,
            $this->rotateButtonOptions + ['data-croppie-rotate-deg' => $degrees]
        );
    }


    protected function renderRotateCcwButton()
    {
        return $this->renderRotateButton($this->rotateCcwLabel, 90);
    }


    protected function renderRotateCwButton()
    {
        return $this->renderRotateButton($this->rotateCwLabel, -90);
    }


    protected function renderImage()
    {
        return Html::tag('img', '', [
            'id' => 'image',
            'src' => '',
            'class' => ['cropper-hidden'],
        ]);
    }


    protected function renderCustomJS()
    {


        $this->view->registerJs(<<<EOJS

EOJS
        );
    }


    protected function renderElement($el)
    {
        $el = $el[0];

        if ($el === '{canvas}') {
            return $this->renderCanvas();
        } elseif ($el === '{custom-js}') {
            return $this->renderCustomJS();
        } elseif ($el === '{rotate-cw}') {
            return $this->renderRotateCwButton();
        } elseif ($el === '{rotate-ccw}') {
            return $this->renderRotateCcwButton();
        } elseif ($el === '{image}') {
            return $this->renderImage();
        } elseif ($el === '{button}') {
            return $this->renderButton();
        }

        \Yii::warning("Unknown layout element: $el", __METHOD__);

        return $el;
    }


    public function run()
    {
        Asset::register($this->view);

        $tag = ArrayHelper::remove($this->options, 'tag', 'div');

        $out = preg_replace_callback(
            '/{[a-z-]+}/',
            [$this, 'renderElement'],
            $this->layout
        );

        // return Html::tag(
        //     $tag,
        //     $out . parent::renderInputHtml('hidden'),
        //     $this->containerOptions
        // );
    }
}
