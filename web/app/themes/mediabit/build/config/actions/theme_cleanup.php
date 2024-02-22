<?php

// Theme cleanup 
function theme_cleanup() {
    if ( ! is_admin() ):
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'rel_alternate');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('rest_api_init', 'wp_oembed_register_route');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
        add_filter('embed_oembed_discover', '__return_false');
    endif;
}
add_action('after_setup_theme', 'theme_cleanup', 100);
// livecanvas friendly
function lc_theme_is_livecanvas_friendly(){} 