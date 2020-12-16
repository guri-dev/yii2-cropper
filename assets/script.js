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
                cropper = new Cropper(image, {
                    aspectRatio: 16 / 9,
                    viewMode: 3,
                });

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
                width: 260,
                height: 260,
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

})(jQuery);
