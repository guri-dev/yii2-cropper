(function($) {
    'use strict';

    if( document.readyState !== 'loading' ) {
        initWidget();
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            initWidget();
        });
    }

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
                image.src = url;
                modal.modal('hide');
                setTimeout(() => {
                    modal.modal('show');
                }, 500);

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

        modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 3,
            });
            }).on('hidden.bs.modal', function () {
                if(cropper !== undefined) {
                   cropper.destroy();
                }
                cropper = null;
            });

        document.getElementById('crop').addEventListener('click', function () {
        var canvas;
        var dataURL;
        modal.modal('hide');

        if (cropper) {
            canvas = cropper.getCroppedCanvas({
                width: 160,
                height: 160,
                minWidth: 256,
                minHeight: 256,
                maxWidth: 4096,
                maxHeight: 4096,
                imageSmoothingQuality: 'high',
            });

            dataURL = canvas.toDataURL("image/png");

            $('input:hidden[name="ImageUpload[image]"]').val(dataURL);
            $('#upload-logo-form').submit();

        }
        });
    }

})(jQuery);
