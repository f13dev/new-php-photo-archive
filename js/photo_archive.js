jQuery(document).ready(function($) {

    $(document).keyup(function(event) {
        if (event.keyCode === 39) {
            $('#lightbox-next').click();
        } else
        if (event.keyCode === 37) {
            $('#lightbox-prev').click();
        } else
        if (event.keyCode === 27) {
            $('#lightbox-close').click();
        }
    });

    $(document).on('click', '.resync_close', function(e) {
        e.preventDefault();
        $('#resync_popup').remove();
    });

    $(document).on('click', '.resync', function(e) {
        e.preventDefault();

        var resync = '<div id="resync_popup"><span class="resync_close">X</span><iframe class="resync_container" src="'+$(this).data('href')+'"></iframe></div>';
        $('body').append(resync);
    });

    $(document).on('click', '.ajax-link', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        var target = $(this).data('target');
        if ($(this).data('folder')) {
            $('#viewing').html('Viewing: '+unescape($(this).data('folder')));
        }
        $(target).html('').css('opacity', '0.5').css('display', 'none');
        $('#lightbox-loading').css('display', 'block');
        $.ajax({
            url: href,
            cache: false,
        }).done(function(output) {
            $(target).html(output).css('opacity', '1');

            var loaded = 0;
            var total = $('.gallery-image').length;

            if (total == 0) {
                check_loaded();
            }

            function check_loaded() {
                console.log(loaded);
                if (loaded == total) {
                    $('#lightbox-loading').css('display', 'none');
                    $(target).css('display', 'block');
                }
            }

            $(".gallery-image")
                .on('load', function() { loaded++; check_loaded(); console.log("image loaded correctly"); })
                .on('error', function() { loaded++; check_loaded(); console.log("error loading image"); })
            ;

        });
    });

    $(document).on('click', '.ajax-image', function(e) {
        e.preventDefault();
        var gallery = $(this).data('gallery');
        var exif = $(this).data('exif');
        var href = $(this).attr('href');
        var number = $(this).data('number');
        var total = $(this).data('total');

        $('#lightbox').css('display', 'block');
        $('#lightbox-loading').css('display', 'none');
        $('#lightbox-content img').css('display', 'none').attr('src', href);
        $('#lightbox-caption').html(exif);
        var next = number + 1;
        var prev = number - 1;
        $('#lightbox-next').data('gallery', gallery).data('exif', $('#image-'+next).data('exif')).attr('href', $('#image-'+next).attr('href')).data('number', $('#image-'+next).data('number')).data('total', $('#image-'+next).data('total'));
        $('#lightbox-prev').data('gallery', gallery).data('exif', $('#image-'+prev).data('exif')).attr('href', $('#image-'+prev).attr('href')).data('number', $('#image-'+prev).data('number')).data('total', $('#image-'+prev).data('total'));

        $('#lightbox-download-link').attr('href', href);

        if (number == total) {
            $('#lightbox-next').css('opacity', '0.5');
        } else {
            $('#lightbox-next').css('opacity', '1');
        }
        if (number == 1) {
            $('#lightbox-prev').css('opacity', '0.5');
        } else {
            $('#lightbox-prev').css('opacity', '1');
        }

            function loaded() {
                    $('#lightbox-loading').css('display', 'none');
                    $('#lightbox-content img').css('display', 'block');
            }

            $("#lightbox-content img")
                .on('load', function() { loaded(); console.log("image loaded correctly"); })
                .on('error', function() { loaded(); console.log("error loading image"); })
            ;
    });

    $(document).on('click', '#lightbox-close', function() {
        $('#lightbox').css('display', 'none');
    });

    $(document).on('click', '#lightcase-prev', function() {

    });

});

jQuery.fn.extend({
    live: function (event, callback) {
       if (this.selector) {
            jQuery(document).on(event, this.selector, callback);
        }
        return this;
    }
});