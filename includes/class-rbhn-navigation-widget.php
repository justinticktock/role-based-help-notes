<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Adds Contents_Page_Navigatio widget.
 */
class RBHN_Contents_Page_Navigation_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rbhn_contents_page_navigation_widget', // Base ID
			__('Help Note Contents Page Navigation', 'role-based-help-notes'), // Name
			array( 'description' => __( 'Add contents page naviation button to sidebar', 'role-based-help-notes' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	
            $role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();

            $post_type = get_post_type( );
            $help_note_object = get_post_type_object( $post_type );
            $help_note_name = $help_note_object->labels->menu_name;
            
            
            $contents_page_id = get_option( 'rbhn_contents_page' ) ;
            
            if ( ! $contents_page_id || ! in_array( $post_type,  $role_based_help_notes->active_help_notes( ) ) || is_archive( ) ) {
                return; 
            }

            echo $args['before_widget'];
            if ( empty ( $instance['title'] ) ) {
                $instance['title'] = esc_html__("Contents", 'role-based-help-notes' );
            }            

            // the $post_type on its own as a value will cause a 404 error as its already used by WordPress
            // so I'm adding a temp postfix "MyPostType"
            $contents_page_link = add_query_arg( 'post_type', "MyPostType{$post_type}", get_permalink( $contents_page_id ) );
            $contents_page_button_text = sprintf( __( '%1$s Notes', 'role-based-help-notes' ), '<strong>' . $help_note_name .'</strong>') ;
            echo $args['before_title'] . $instance['title'] . "" . $args['after_title'];
            echo   '<button id="contents-button1" class="readmorebtn" onclick="' . esc_attr('window.location="' . $contents_page_link . '"') . '">' . $contents_page_button_text . '</button></BR></BR>';
            echo $args['after_widget'];
        }

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) && ( $instance[ 'title' ] != "" )) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '';
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class RBHN_Contents_Page_Navigation_Widget