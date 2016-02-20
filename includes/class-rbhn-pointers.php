<?php
	
if ( !class_exists( "RBHN_Pointers" ) )
{
    class RBHN_Pointers
    {
        function RBHN_Pointers() // Constructor
        {
                // This adds scripts for ANY admin screen
                add_action( 'admin_enqueue_scripts', array( $this, 'RBHN_Pointers_admin_scripts' ) );
        }

        
	/**
	 * Print the pointer JavaScript data.
	 *
	 * @since 3.3.0
	 *
	 * @param string $pointer_id The pointer ID.
	 * @param string $selector The HTML elements, on which the pointer should be attached.
	 * @param array  $args Arguments to be passed to the pointer JS (see wp-pointer.js).
	 */
	private static function print_js( $pointer_id, $selector, $args ) {
		if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) ) {
			return;
                }
		?>
		<script type="text/javascript">
		(function($){
			var options = <?php echo wp_json_encode( $args ); ?>, setup;

			if ( ! options )
				return;

			options = $.extend( options, {
				close: function() {
					$.post( ajaxurl, {
						pointer: '<?php echo $pointer_id; ?>',
						action: 'dismiss-wp-pointer'
					});
				}
			});

			setup = function() {
				$('<?php echo $selector; ?>').first().pointer( options ).pointer('open');
			};

			if ( options.position && options.position.defer_loading )
				$(window).bind( 'load.wp-pointers', setup );
			else
				$(document).ready( setup );

		})( jQuery );
		</script>
		<?php
	}

        function RBHN_Pointers_admin_scripts() {
            
                // WordPress Pointer Handling
                // find out which pointer ids this user has already seen
                $seen_it = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
                // at first assume we don't want to show pointers
                $do_add_script = false;

                
                // Only show one point at a time
                if ( ! in_array( 'rbhn-contents-button-pointer', $seen_it ) ) {
                    // if contents-button for pointer requirement

                    $do_add_script = true;

                    // hook to output pointer script
                    add_action( 'admin_print_footer_scripts', array( $this, 'pointer_rbhn_content_button_footer_script' ) );
                        
                } elseif ( ! in_array( 'rbhn-add-media-button-pointer', $seen_it ) ) {
                    // if add-media-button for pointer requirement
                    
                    $do_add_script = true;

                    // hook to output pointer script
                    add_action( 'admin_print_footer_scripts', array( $this, 'pointer_rbhn_add_media_footer_script' ) );
                }
                
                if ( $do_add_script ) {
                        // add JavaScript for WP Pointers
                        wp_enqueue_script( 'wp-pointer' );
                        // add CSS for WP Pointers
                        wp_enqueue_style( 'wp-pointer' );
                }
        }
		
        // content_button pointer has its own function responsible for putting appropriate JavaScript into footer
        function pointer_rbhn_content_button_footer_script() {

            $pointer_content = '<h3>' . __('Front of site Content Page', 'role-based-help-notes') . '.</h3>'; 
            $pointer_content .= '<p>'. __('This provides a quick link to the front of site contents page.  Useful when dealing with lots of Help Notes.', 'role-based-help-notes') ;

            $position = array( 'edge' => 'top', 'align' => 'left', 'my' => 'left top', 'at' => 'left bottom' );
            

            self::print_js( 'rbhn-contents-button-pointer', '#contents-button1', array( 
                'content' => $pointer_content,
                'position' => $position,
            ) );

        } 

        // content_button pointer has its own function responsible for putting appropriate JavaScript into footer
        function pointer_rbhn_add_media_footer_script() {

            $pointer_content = '<h3>' . __('Add Media', 'role-based-help-notes') . '.</h3>'; 
            $pointer_content .= '<p>'. __('This allows you to add attachments and images into your Help Notes.', 'role-based-help-notes') ;

            $position = array( 'edge' => 'left', 'align' => 'center', 'my' => 'left middle', 'at' => 'right bottom-10' );

            self::print_js( 'rbhn-add-media-button-pointer', '#insert-media-button', array( 
                'content' => $pointer_content,
                'position' => $position,
            ) );

        }         
    }
}

// Instantiating the Class
if ( class_exists( "RBHN_Pointers" ) ) {
	$RBHN_Pointers = new RBHN_Pointers( );
}