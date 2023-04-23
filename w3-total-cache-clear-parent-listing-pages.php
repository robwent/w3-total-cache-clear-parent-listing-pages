<?php
/*
Plugin Name: W3 Total Cache Clear Parent Term Listing Pages
Description: Forcing W3 Total Cache to clear parent term listing pages on post save/update
 * Version: 1.0
 * Author: Robert Went
 * Author URI: https://robertwent.com
 * License: GPL3
*/

add_action( 'save_post', 'rw_clear_total_cache_terms_pages', 10, 2 );
add_action( 'delete_post', 'rw_clear_total_cache_terms_pages', 10, 2 );
add_action( 'publish_post', 'rw_clear_total_cache_terms_pages', 10, 2 );
add_action( 'draft_post', 'rw_clear_total_cache_terms_pages', 10, 2 );

/**
 * Clear Total Cache term listing pages for a post
 * @param $post_id
 * @param $post
 *
 * @return void
 */
function rw_clear_total_cache_terms_pages( $post_id, $post ) {
    if ( function_exists( 'w3tc_flush_url' ) ) {
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            if ( $terms ) {
                foreach ( $terms as $term ) {
                    $urls = rw_get_post_term_urls( $term, $taxonomy );
                    if ( ! empty( $urls ) ) {
                        foreach ( $urls as $url ) {
                            w3tc_flush_url( $url );
                        }
                    }
                }
            }
        }
    }
}

/**
 * Recursively get the URLs of all terms up to the parent
 * @param $term
 * @param $taxonomy
 * @param $urls
 *
 * @return array|mixed
 */
function rw_get_post_term_urls( $term, $taxonomy, $urls = array() ) {
    if ( $term ) {
        $urls[] = get_term_link( $term, $taxonomy );
        if ( $term->parent ) {
            $urls = array_merge( $urls, rw_get_post_term_urls( $term->parent, $taxonomy ), $urls );
        } else {
            return $urls;
        }
    }

    return $urls;
}
