(function($) {

    function GetURLParameter( sParam ) {
            var sPageURL = window.location.search.substring( 1 );
            var sURLVariables = sPageURL.split( '&' );
            for ( var i = 0; i < sURLVariables.length; i++ ) {
                    var sParameterName = sURLVariables[i].split('=');
                    if ( sParameterName[0] == sParam ) {
                            return sParameterName[1].replace('MyPostType', '');
                    }
            }
    }

    function ClickTabbyTab( ) {
        setTimeout(function() {
            var tabby_tab_id = GetURLParameter( 'tabby_tab' );
            if (!tabby_tab_id)
                    return false;

            $("#tablist1-" + tabby_tab_id).click();	 

        }, 500);
    }

    function GotoTabbyTab( ) {
        setTimeout(function() {

            var $currentActive = $('html, body').find('.responsive-tabs__heading--active');  
            var newActivePos = ($currentActive.offset().top) - 15;

            $('html, body').animate({ scrollTop: newActivePos }, 200)

        }, 600);
    }
    
    $(window).load(ClickTabbyTab( )).ready(GotoTabbyTab( ));  

})(jQuery);