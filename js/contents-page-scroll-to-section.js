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

    function goto_section( ){

            var post_type = "#" + arguments[0];

            $('html, body').animate({
                    scrollTop: $(post_type).offset().top -50
            }, 'slow');
    }

    $(document).ready(function(){

            var post_type = GetURLParameter( 'post_type' );
            
            if ( post_type ) {
                goto_section( post_type );
            }            
            
    })


})(jQuery);