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
        var file = $(this).data('file');
        var exif = $(this).data('exif');
        var href = $(this).attr('href');
        var number = $(this).data('number');
        var total = $(this).data('total');
        var ext = $(this).data('ext');
        var db_id = $(this).data('db-id');
        var description = $(this).data('description');
        var tags = $(this).data('tags');
        var elem = '';

        $('#lightbox').css('display', 'block');
        $('#lightbox-loading').css('display', 'none');


        ext = href.substr( (href.lastIndexOf('.') +1) ).toLowerCase();

        $('#lightbox-content').html('');
        if (ext == 'jpg' || ext == 'png' || ext == 'gif') {
            elem = '<img src="'+href+'" id="lightbox-image">';
        } else 
        if (ext == 'mp4') {
            elem = '<video controls class="lightbox-image"><source src="'+href+'" type="video/mp4"></video>';
        } else {
            elem = '<span class="lightbox-image">Unsupported file, please re-sync the gallery to convert this file.</span>';
        }

        $('#lightbox-content').html(elem);
        /*
        $('#lightbox-content').html('');

        if (ext == 'jpg' || ext == 'png' || ext == 'gif') {
            elem = '<img src="'+href+'" id="lightbox-image">';
            console.log('extension: '+ext);
        } else 
        if (ext == 'mov') {
            elem = '<embed id="lightbox-image" src="'+href+'" type="video/quicktime" style="width:320px;height:240px;"><noembed>Your browser does not support this media object or the embed element.</noembed></embed>';
        }   

        $('#lightbox-content').html(elem);
        */
        //$('#lightbox-content img').css('display', 'none').attr('src', href);
        $('#lightbox-caption-text').html(exif);
        $('#lightbox-caption-description').html(atob(description));   
        var next = number + 1;
        var prev = number - 1;
        $('#lightbox-next').data('gallery', gallery).data('exif', $('#image-'+next).data('exif')).attr('href', $('#image-'+next).attr('href')).data('number', $('#image-'+next).data('number')).data('total', $('#image-'+next).data('total')).data('db-id', $('#image-'+next).data('db-id')).data('description', $('#image-'+next).data('description')).data('tags', $('#image-'+next).data('tags')).data('file', $('#image-'+next).data('file'));
        $('#lightbox-prev').data('gallery', gallery).data('exif', $('#image-'+prev).data('exif')).attr('href', $('#image-'+prev).attr('href')).data('number', $('#image-'+prev).data('number')).data('total', $('#image-'+prev).data('total')).data('db-id', $('#image-'+prev).data('db-id')).data('description', $('#image-'+prev).data('description')).data('tags', $('#image-'+prev).data('tags')).data('file', $('#image-'+prev).data('file'));
        $('#edit_description').data('db-id', db_id).data('folder-name', gallery).data('file-name', file);  

        console.log('DB_ID '+db_id);   
        console.log('Gallery '+gallery);
        console.log('File: '+file);        
        
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
        $('#lightbox-content').html('');
    });

    $(document).on('click', '#lightcase-prev', function() {

    });

    $(document).on('click', '#edit_description', function() {
        // Load a form into $('#lightbox-caption-description');

        var ajax_url = $(this).data('ajax-url');
        var file = $(this).data('file-name');
        var folder = $(this).data('folder-name');

        var ajax_url = ajax_url+'file='+encodeURIComponent(file)+'&folder='+encodeURIComponent(folder);
        
        $.ajax({
            url: ajax_url,
            cache: false,
        }).done(function(output) {
            $('#lightbox-caption-description').html(output);
        });
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