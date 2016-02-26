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
    
    
 // $('.dashicons-edit').hide();
 // $('.dashicons-edit').show();  
 //   $(function() {
        
  //  $('rbhn-link').hover(function() {
   // $("*[class*='page-item-']").hover(function() { 
   //  $('.dashicons-edit').show();
    $("*[class*='rbhn-link']").hover(function() { 

        
var current_class = $(this).attr('class'); 
//console.log(current_class);   


     // $(this).closest('.dashicons-edit').fadeIn(); 
       //$('.dashicons-edit').stop(true, true).fadeIn(); 

   //   $('a.rbhn-link').prevAll('a.dashicons-edit:first').fadeIn(); 
        
      // $(this).parent('.dashicons-edit').appendTo(this);
      // $(this).closest('.dashicons-edit').appendTo(this);
       //$(this).find('.dashicons-edit').appendTo(this);
      // $(this).find('.dashicons-edit').appendTo($(this).find('.rbhn-link:first'));
      // $(this).find('.dashicons-edit').stop(true, true).fadeIn(); 
     // $(this).find('.dashicons-edit').stop(true, true).fadeIn(); 

     // $(this).find('.dashicons-edit').stop(true, true).fadeIn(); 

 $(this).siblings('.dashicons-edit').fadeIn('fast'); 

//$('a.rbhn-link').prevAll('a.dashicons-edit:first').fadeIn(); 
    //   exit();
//var edit_icon = $('a.rbhn-link').prevAll('a.dashicons-edit:first').detach();

//$('a.rbhn-link').prevAll('a.dashicons-edit:first').fadeIn(); 
//$(this).append(edit_icon);



var previous_edit_icon =  $('li.rbhn-link').prevAll('li.dashicons-edit:first').attr('class'); 
//console.log(previous_edit_icon);    
var this_link_name =  $(this).attr('class'); 
//console.log(this_link_name);  

//http://stackoverflow.com/questions/2310270/jquery-find-closest-previous-sibling-with-class
//$('a.rbhn-link').prevAll('a.dashicons-edit:first').appendTo(this);
   //   $(this).prepend($('a.rbhn-link').prevAll('a.dashicons-edit:first'));
       

    }, function() { 
       // $(this).closest('.dashicons-edit').fadeOut(); 
       // $('.dashicons-edit').stop(true, true).fadeOut(); 
    
     // $(this).find('.dashicons-edit').remove($(this).find('.rbhn-link'));
     
    // $(this).siblings('.dashicons-edit').stop(true, true).fadeOut(); 
    
  //  $('li.dashicons-edit').prevAll('li.rbhn-link:first').appendTo(this);
    
//$('.dashicons-edit').hover(function() { 

$('.dashicons-edit').not(".Class").hover(function() { 

var this_link_name =  $(this).attr('class'); 
console.log(this_link_name);  
//$(this).siblings('.dashicons-edit').stop(true, true).fadeIn(); 

}, function() { 

//$(this).siblings('.dashicons-edit').stop(true, true).fadeOut(); 
$('.dashicons-edit').fadeOut(); 
});

    });




   // $('#h_administrator').hover(function(){
       // $('#h_administrator').fadeToggle();
    //});


})(jQuery);