(function($) {

    // make edit icon visible when over a link
    $("*[class*='rbhn-link']").hover(function() { 
        $(this).siblings('.dashicons-edit').delay( 500 ).fadeIn('fast'); 
    }, function() { 
        $('.dashicons-edit').delay( 100 ).stop( true, true ).hide(0)
    });

    // stop hide of edit icon if on it.
    $('.dashicons-edit').hover(function() { 
        $(this).stop().show();
    });

})(jQuery);