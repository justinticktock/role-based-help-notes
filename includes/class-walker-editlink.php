<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



/**
 * walker class used to create an HTML list on the Help Notes Contents Page
 *
 * @see Walker
 */
class RBHN_EditLinks extends Walker_Page 
{


    /**
     * @see Walker::start_el()
     * 
     * @param string $output       Passed by reference. Used to append additional content.
     * @param object $page         Page data object.
     * @param int    $depth        Depth of page. Used for padding.
     * @param int    $current_page Page ID.
     * @param array  $args
     */
    public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
        
            if ( $depth ) {
                    $indent = str_repeat( "\t", $depth );
            } else {
                    $indent = '';
            }

            $css_class = array( 'page_item', 'page-item-' . $page->ID );

            if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
                    $css_class[] = 'page_item_has_children';
            }

            if ( ! empty( $current_page ) ) {
                    $_current_page = get_post( $current_page );
                    if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
                            $css_class[] = 'current_page_ancestor';
                    }
                    if ( $page->ID == $current_page ) {
                            $css_class[] = 'current_page_item';
                    } elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
                            $css_class[] = 'current_page_parent';
                    }
            } elseif ( $page->ID == get_option('page_for_posts') ) {
                    $css_class[] = 'current_page_parent';
            }

            $css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

            if ( '' === $page->post_title ) {
                    /* translators: %d: ID of a post */
                    $page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );
            }

            $args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
            $args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

            /** This filter is documented in wp-includes/post-template.php */
            $output .= $indent;
            $output .= sprintf(
                    '<li class="%s"><a class="%s" href="%s">%s%s%s</a>',
                    $css_classes,
                    'rbhn-link ',
                    get_permalink( $page->ID ),
                    $args['link_before'],
                    apply_filters( 'the_title', $page->post_title, $page->ID ),
                    $args['link_after']
             );
            $output .= sprintf(
                    '<a class="%s" title="%s" style="%s" href="%s">%s%s%s</a>',
                    'dashicons dashicons-edit',
                    _x( "edit", 'the hover prompt for the edit icon on the Content page index.', 'role-based-help-notes' ),
                    'display: none',
                    admin_url( 'post.php?action=edit&post=' . $page->ID ),
                    '', //$args['link_before'],
                     '', //apply_filters( 'the_title', $page->post_title, $page->ID ),
                    $args['link_after']
             );

    }

}