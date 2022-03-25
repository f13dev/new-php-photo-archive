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
                if (loaded == total) {
                    $('#lightbox-loading').css('display', 'none');
                    $(target).css('display', 'block');
                }
            }

            $(".gallery-image")
                .on('load', function() { loaded++; check_loaded();})
        .on('error', function() { loaded++; check_loaded();})
            ;

        });
    });

    $(document).on('click', '.ajax-image', function(e) {
        e.preventDefault();
        var gallery = $(this).data('gallery');
        var file = $(this).data('file');
        var folder = $(this).data('folder');
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
        $('#lightbox-caption-text').html(exif);
        $('#lightbox-caption-description').html(atob(description)); 
        
        var tag_decode = atob(tags);
        tag_decode = JSON.parse(tag_decode);
        tag_decode = JSON.parse(tag_decode);
        var tag_string = '';
        $.each(tag_decode, function(i, tag) {
            var elem = '<span class="tag">'+tag.tag+'</span>';
            tag_string = tag_string+elem;
        });
        $('#lightbox-caption-tags').html(tag_string);

        var next = number + 1;
        var prev = number - 1;
        $('#lightbox-next').data('folder', $('#image-'+next).data('folder')).data('gallery', $('#image-'+next).data('folder')).data('exif', $('#image-'+next).data('exif')).attr('href', $('#image-'+next).attr('href')).data('number', $('#image-'+next).data('number')).data('total', $('#image-'+next).data('total')).data('db-id', $('#image-'+next).data('db-id')).data('description', $('#image-'+next).data('description')).data('tags', $('#image-'+next).data('tags')).data('file', $('#image-'+next).data('file'));
        $('#lightbox-prev').data('folder', $('#image-'+prev).data('folder')).data('gallery', $('#image-'+prev).data('folder')).data('exif', $('#image-'+prev).data('exif')).attr('href', $('#image-'+prev).attr('href')).data('number', $('#image-'+prev).data('number')).data('total', $('#image-'+prev).data('total')).data('db-id', $('#image-'+prev).data('db-id')).data('description', $('#image-'+prev).data('description')).data('tags', $('#image-'+prev).data('tags')).data('file', $('#image-'+prev).data('file'));
        $('#edit_description').data('db-id', db_id).data('folder-name', folder).data('file-name', file).data('number', number);  
        $('#edit_tags').data('db-id', db_id).data('folder-name', folder).data('file-name', file).data('number', number);    
        
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
                .on('load', function() { loaded();})
        .on('error', function() { loaded();})
            ;
    });

    $(document).on('click', '#lightbox-close', function() {
        $('#lightbox').css('display', 'none');
        $('#lightbox-content').html('');
    });

    $(document).on('click', '#lightcase-prev', function() {

    });

    $(document).on('click', '#edit_description', function() {
        var ajax_url = $(this).data('ajax-url');
        var file = $(this).data('file-name');
        var folder = $(this).data('folder-name');
        var number = $(this).data('number');

        var ajax_url = ajax_url+'file='+encodeURIComponent(file)+'&folder='+encodeURIComponent(folder)+'&number='+encodeURIComponent(number);
    
        $.ajax({
            url: ajax_url,
            cache: false,
        }).done(function(output) {
            $('#lightbox-caption-description').html(output);
        }).fail(function(d) {
            alert('Ajax loading failed');
        });
    });

    $(document).on('click', '#edit_tags', function() {
        var ajax_url = $(this).data('ajax-url');
        var file = $(this).data('file-name');
        var folder = $(this).data('folder-name');
        var number = $(this).data('number');

        var ajax_url = ajax_url+'file='+encodeURIComponent(file)+'&folder='+encodeURIComponent(folder)+'&number='+encodeURIComponent(number);
        
        $.ajax({
            url: ajax_url,
            cache: false,
        }).done(function(output) {
            $('#lightbox-caption-tags').html(output);
        }).fail(function(d) {
            alert('Ajax loading failed');
        });
    });

    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        var method = $(this).attr('method');

        var submit = $(this).children('input[type="submit"]');
        submit.css('display', 'none');    

        var id = $(this).attr('id');

        if (id == 'edit_description_form') {
            var description = $(this).children('textarea[name="description"]').val();
            var number = $(this).children('input[name="number"]').val();
        } else 
        if (id == 'edit_tags_form') {
            $('#add_tags_input').trigger('add_tag');

            var values = $("input[name='tags[]']")
            .map(function(){return $(this).val();}).get(); 
            
            let obj = [];
            values.forEach(function(value, key) {
                var eachTag = {'tag' : value};
                obj.push(eachTag);
            });

            values = JSON.stringify(obj)
            var number = $(this).children('input[name="number"]').val();
        }

        var formData = new FormData(this);
        var url = $(this).data('url');

        $.ajax({
            type : method,
            url : url,
            data : formData,
            processData: false,
            contentType: false,
        }).done(function(d) {
            $(target).html(d);
            submit.css('display', 'unset');
            // Update the description on the loaded image data
            if (id == 'edit_description_form') {
                var elem = '#image-'+number;
                $(elem).data('description', btoa(description));
            } else 
            if (id == 'edit_tags_form') {
                var elem = '#image-'+number;
                $(elem).data('tags', btoa(JSON.stringify(values)));
            }
        }).fail(function(d) {
            alert('An error occured.');
        });
    });


    // Start of tags
    $(document).on('add_tag', '#add_tags input', function() {
        var txt = this.value.replace(/[^a-zA-Z0-9\s\+\-\.\#]/ig,''); // allowed characters
        if(txt) $("<span/>", {html:txt+'<input type="hidden" name="tags[]" value="'+txt+'">', insertBefore:this});
        this.value = "";
        $('#add_tags_input').focus();
    });

    $(document).on('add_tag', 'input[name="search_term"]', function() {
        var txt = this.value.replace(/[^a-zA-Z0-9\s\+\-\.\#]/ig,''); // allowed characters
        if (txt) $("<span/>", {html:txt+'<input type="hidden" name="tags[]" value="'+txt+'">', insertBefore:this});
        this.value = "";
        $(this).focus();
    })

    $(document).on('keyup', 'input[name="search_term"]', function(ev) {
        var suggest = $('#search_term_suggest');
        if (/(188|13)/.test(ev.which)) {
            $(this).trigger("add_tag");
        } else 
        if (this.value != '') {
            var ajax = $(this).data('ajax');
            var url = ajax+'do=suggest_tag&text='+encodeURIComponent(this.value);
            $.ajax({
                type: 'GET',
                url: url,
            }).done(function(d) {
                $(suggest).html(d).css('display', 'block');
            }).fail(function(d) {
                alert('An error ocurred.');
            });
        } else {
            $(suggest).html('').css('display', 'none');
        }
    });

    $(document).on('keyup', '#add_tags_input', function(ev) {
        if(/(188|13)/.test(ev.which)) {
            $(this).trigger("add_tag"); 
        } else 
        var suggest = $('#add_tags_suggest');
        if (this.value != '') {
            var ajax = $(this).data('ajax');
            var url = ajax+'do=suggest_tag&text='+encodeURIComponent(this.value);
            // Ajax call to get tag suggestions

            $.ajax({
                type : 'GET',
                url : url,
            }).done(function(d) {
                $(suggest).html(d).css('display', 'block');
            }).fail(function(d) {
                alert('An error ocurred.');
            });
        } else {
            $(suggest).html('').css('display', 'none');
        }
    });

    $(document).on('click', '#search_term_suggest div', function() {
        var input = $('input[name="search_term"]');
        var tag = $(this).html();

        input.val(tag);
        input.trigger('add_tag');

        $('#search_term_suggest').html('').hide();
    })

    $(document).on('click', '#add_tags_suggest div', function() {
        var input = $('#add_tags_input');
        var tag = $(this).html();

        input.val(tag);
        input.trigger('add_tag');

        $('#add_tags_suggest').html('').hide();
    });

    $(document).on('click', '#add_tags span', function() {
        if(confirm("Remove "+ $(this).text() +"?")) $(this).remove(); 
    });

    $(document).on('click', '#search span', function() {
        $(this).remove();
    })

    $(document).on('submit', '#search', function(e) {
        e.preventDefault();
        var url = $(this).data('href');
        var term = $(this).children('input[name="search_term"]').val();
        var method = $(this).attr('method');
        var formData = new FormData(this);

        $('#lightbox-loading').css('display', 'block');

        var terms = $('input[name^=tags]').map(function(idx, elem) {
            return $(elem).val();
          }).get();

          var term = '';
          terms.forEach(function(value, key) {
              if (term == '') {
                  term = value;
              } else {
                  term = term+', '+value;
              }
          });

        if (term != '') {
            $.ajax({
                type : method,
                url : url,
                data : formData,
                processData: false,
                contentType: false,
            }).done(function(d) {
                $('#container').html(d);
                $('#viewing').html('Search results: '+unescape(term));
                $('#search').slideToggle('slow');
                $('#lightbox-loading').css('display', 'none');
            }).fail(function(d) {
                alert('An error ocurred.');
                $('#lightbox-loading').css('display', 'none');
            });
        }
    });

    $(document).on('click', '#search-toggle', function() {
        $('#search').slideToggle('slow');
        $('input[name="search_term"]').focus();
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