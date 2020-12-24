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

    protected function renderCustomCSS()
    {
        $this->view->registerCss(".cropper-view-box,
        .img-container img {
            max-width: 100%;
        }
        ");
    }

    protected function renderCustomCSSCircle()
    {
        $this->view->registerCss(".cropper-view-box,
        .cropper-face {
          border-radius: 50%;
        }
        ");
    }

    protected function renderCustomJS()
    {

        $js_options = $this->clientOptions
            ? Json::encode($this->clientOptions)
            : '';
        $js_options = str_replace('"','',$js_options);
        $this->view->registerJs(<<<EOJS
        if( document.readyState !== 'loading' ) {
            initWidget();
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                initWidget();
            });
        }


        var width;
        var height;

        function initWidget() {
            var image = document.getElementById('image');
            var input = $('#imageupload-image');
            var modal = $('#w0');
            if(modal.length == 0) {
                var modal = $('#w9-modal');
            }

            var cropper;

            input.on('change', function (e) {
                var files = e.target.files;
                var done = function (url) {
                    input.value = '';
                    if(cropper) {
                        cropper.destroy();
                    }
                    cropper.destroy();
                    image.src = url;
                    cropper = new Cropper(image,
                        $js_options
                    );

                };
                var reader;
                var file;
                var url;

                if (files && files.length > 0) {
                    file = files[0];

                    if (URL) {
                    done(URL.createObjectURL(file));
                    } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                    }
                }

            });

            image.addEventListener('crop', function (event) {
                width = event.detail.width;
                height = event.detail.height;
            });

            modal.on('hidden.bs.modal', function () {
                if(cropper !== undefined ) {
                   cropper.destroy();
                }
                $(this).find('#image').attr('src','');
                $(this).find('#imageupload-image').val('');

            });

            document.getElementById('crop').addEventListener('click', function () {
            var canvas;
            var dataURL;
            modal.modal('hide');

            if (cropper) {
                canvas = cropper.getCroppedCanvas({
                    width: width,
                    height: height,
                    minWidth: 256,
                    minHeight: 256,
                    maxWidth: 4096,
                    maxHeight: 4096,
                    imageSmoothingEnabled: false,
                    imageSmoothingQuality: 'high',
                });

                dataURL = canvas.toDataURL("image/png");

                $('input:hidden[name="ImageUpload[image]"]').val(dataURL);
                $('#upload-logo-form').submit();

            }
            });
        }
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
        } elseif ($el === '{custom-css}') {
            return $this->renderCustomCSS();
        } elseif ($el === '{custom-css-circle}') {
            return $this->renderCustomCSSCircle();
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
